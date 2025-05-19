<?php

namespace App\Domain\Subscription\Services;

use App\Application\Subscription\DTOs\ConfirmSubscriptionRequestDTO;
use App\Application\Subscription\DTOs\UnsubscribeRequestDTO;
use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Subscription\Events\SubscriptionConfirmed;
use App\Domain\Subscription\Events\SubscriptionCreated;
use App\Domain\Subscription\Events\SubscriptionUnsubscribed;
use App\Domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use App\Domain\Subscription\ValueObjects\Email;
use App\Domain\Subscription\ValueObjects\Frequency;
use App\Domain\Subscription\ValueObjects\Token;
use App\Domain\Weather\Repositories\WeatherRepositoryInterface;
use App\Domain\Weather\ValueObjects\City;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\Custom\EmailAlreadySubscribedException;
use App\Exceptions\Custom\SubscriptionAlreadyPendingException;
use App\Exceptions\Custom\TokenNotFoundException;

class SubscriptionService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly WeatherRepositoryInterface $weatherRepository,
    ) {
    }

    /**
     * @throws CityNotFoundException
     * @throws EmailAlreadySubscribedException
     * @throws SubscriptionAlreadyPendingException
     */
    public function subscribe(Email $email, City $city, Frequency $frequency): Subscription
    {
        if (!$this->weatherRepository->cityExists($city)) {
            throw new CityNotFoundException();
        }

        $subEntity = new Subscription($email, $city, $frequency);
        if ($this->subscriptionRepository->hasActiveSubscription($subEntity)) {
            throw new EmailAlreadySubscribedException();
        }

        $existingPending = $this->subscriptionRepository->getPendingSubscription($subEntity);
        if ($existingPending) {
            $tokenStillValid = $this->subscriptionRepository->hasValidConfirmationToken($existingPending->id);

            if ($tokenStillValid) {
                throw new SubscriptionAlreadyPendingException();
            }

            $newConfirmToken = Token::createConfirmation();
            $newCancelToken = Token::createUnsubscribe();

            $this->subscriptionRepository->replaceTokensForPending(
                $existingPending->id,
                $newConfirmToken,
                $newCancelToken
            );

            $subEntity->setId($existingPending->id);
            $subEntity->setConfirmationToken($newConfirmToken);
            $subEntity->setUnsubscribeToken($newCancelToken);

            SubscriptionCreated::dispatch($subEntity);

            return $subEntity;
        }

        $subEntity->setConfirmationToken(Token::createConfirmation());
        $subEntity->setUnsubscribeToken(Token::createUnsubscribe());

        $subscription = $this->subscriptionRepository->save($subEntity);

        SubscriptionCreated::dispatch($subscription);

        return $subscription;
    }

    /**
     * @throws TokenNotFoundException
     */
    public function confirmSubscription(ConfirmSubscriptionRequestDTO $dto): ?Subscription
    {
        $confirmedSubscription = $this->subscriptionRepository->confirmSubscriptionByToken(
            $dto->confirmationToken->getValue()
        );

        if (!$confirmedSubscription) {
            throw new TokenNotFoundException();
        }

        SubscriptionConfirmed::dispatch($confirmedSubscription);

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

//        SubscriptionUnsubscribed::dispatch($removedSubscription);

        return true;
    }
}
