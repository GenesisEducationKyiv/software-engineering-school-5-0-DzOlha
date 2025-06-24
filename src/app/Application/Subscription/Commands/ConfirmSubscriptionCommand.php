<?php

namespace App\Application\Subscription\Commands;

use App\Application\Subscription\DTOs\ConfirmSubscriptionRequestDTO;
use App\Application\Subscription\Services\SubscriptionServiceInterface;
use App\Exceptions\Custom\TokenNotFoundException;

class ConfirmSubscriptionCommand
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptionService
    ) {
    }

    /**
     * @throws TokenNotFoundException
     */
    public function execute(ConfirmSubscriptionRequestDTO $dto): bool
    {
        $subscription = $this->subscriptionService->confirmSubscription($dto);
        return $subscription !== null;
    }
}
