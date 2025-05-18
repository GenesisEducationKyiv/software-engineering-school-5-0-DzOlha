<?php

namespace App\Application\Subscription\Commands;

use App\Application\Subscription\DTOs\CreateSubscriptionRequestDTO;
use App\Domain\Subscription\Services\SubscriptionService;
use App\Domain\Subscription\ValueObjects\Email;
use App\Domain\Subscription\ValueObjects\Frequency;
use App\Domain\Weather\ValueObjects\City;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\Custom\EmailAlreadySubscribedException;
use App\Exceptions\Custom\SubscriptionAlreadyPendingException;

class CreateSubscriptionCommand
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    /**
     * @throws SubscriptionAlreadyPendingException
     * @throws EmailAlreadySubscribedException
     * @throws CityNotFoundException
     */
    public function execute(CreateSubscriptionRequestDTO $dto): string
    {
        $subscription = $this->subscriptionService->subscribe(
            new Email($dto->email),
            new City($dto->city),
            Frequency::fromName($dto->frequency)
        );

        return $subscription->getConfirmationToken()->getValue();
    }
}
