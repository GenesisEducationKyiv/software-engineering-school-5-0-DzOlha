<?php

namespace App\Modules\Subscription\Application\Services;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\Custom\EmailAlreadySubscribedException;
use App\Exceptions\Custom\FrequencyNotFoundException;
use App\Exceptions\Custom\SubscriptionAlreadyPendingException;
use App\Exceptions\Custom\TokenNotFoundException;
use App\Modules\Subscription\Application\DTOs\ConfirmSubscriptionRequestDTO;
use App\Modules\Subscription\Application\DTOs\UnsubscribeRequestDTO;
use App\Modules\Subscription\Application\Messaging\Publishers\EventPublisherInterface;
use App\Modules\Subscription\Application\Messaging\Events\SubscriptionConfirmed;
use App\Modules\Subscription\Application\Messaging\Events\SubscriptionCreated;
use App\Modules\Subscription\Domain\Entities\Subscription;
use App\Modules\Subscription\Domain\Repositories\SubscriptionRepositoryInterface;
use App\Modules\Subscription\Domain\ValueObjects\City\City;
use App\Modules\Subscription\Domain\ValueObjects\Email\Email;
use App\Modules\Subscription\Domain\ValueObjects\Frequency\Frequency;
use App\Modules\Subscription\Domain\ValueObjects\Token\Factory\TokenFactoryInterface;
use App\Modules\Weather\Presentation\Interface\WeatherModuleInterface;

class SubscriptionService implements SubscriptionServiceInterface
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly WeatherModuleInterface $weatherModule,
        private readonly TokenFactoryInterface $tokenFactory,
        private readonly EventPublisherInterface $eventPublisher
    ) {
    }

    /**
     * @throws CityNotFoundException
     * @throws EmailAlreadySubscribedException
     * @throws SubscriptionAlreadyPendingException
     * @throws ApiAccessException
     * @throws FrequencyNotFoundException
     */
    public function subscribe(Email $email, City $city, Frequency $frequency): Subscription
    {
        if (!$this->weatherModule->cityExists($city->getName())) {
            throw new CityNotFoundException();
        }

        $subEntity = new Subscription($email, $city, $frequency);
        if ($this->subscriptionRepository->hasActiveSubscription($subEntity)) {
            throw new EmailAlreadySubscribedException();
        }

        $existingPendingId = $this->subscriptionRepository->getPendingSubscriptionId($subEntity);
        if ($existingPendingId) {
            $tokenStillValid = $this->subscriptionRepository->hasValidConfirmationToken($existingPendingId);

            if ($tokenStillValid) {
                throw new SubscriptionAlreadyPendingException();
            }

            $newConfirmToken = $this->tokenFactory->createConfirmation();
            $newCancelToken = $this->tokenFactory->createCancel();

            $this->subscriptionRepository->replaceTokensForPending(
                $existingPendingId,
                $newConfirmToken,
                $newCancelToken
            );

            $subEntity->setId($existingPendingId);
            $subEntity->setConfirmationToken($newConfirmToken);
            $subEntity->setUnsubscribeToken($newCancelToken);

            $this->eventPublisher->publish(new SubscriptionCreated($subEntity));

            return $subEntity;
        }

        $subEntity->setConfirmationToken($this->tokenFactory->createConfirmation());
        $subEntity->setUnsubscribeToken($this->tokenFactory->createCancel());

        $subscription = $this->subscriptionRepository->save($subEntity);

        $this->eventPublisher->publish(new SubscriptionCreated($subscription));

        return $subscription;
    }

    /**
     * @throws TokenNotFoundException
     */
    public function confirmSubscription(ConfirmSubscriptionRequestDTO $dto): Subscription
    {
        $confirmedSubscription = $this->subscriptionRepository->confirmSubscriptionByToken(
            $dto->confirmationToken->getValue()
        );

        if (!$confirmedSubscription) {
            throw new TokenNotFoundException();
        }

        $this->eventPublisher->publish(new SubscriptionConfirmed($confirmedSubscription));

        return $confirmedSubscription;
    }

    /**
     * @throws TokenNotFoundException
     */
    public function unsubscribe(UnsubscribeRequestDTO $dto): ?bool
    {
        $removedSubscription = $this->subscriptionRepository->unsubscribeByToken(
            $dto->cancelToken->getValue()
        );

        if (!$removedSubscription) {
            throw new TokenNotFoundException();
        }

        return true;
    }
}
