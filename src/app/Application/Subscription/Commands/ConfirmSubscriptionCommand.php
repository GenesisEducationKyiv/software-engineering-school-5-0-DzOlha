<?php

namespace App\Application\Subscription\Commands;

use App\Application\Subscription\DTOs\ConfirmSubscriptionRequestDTO;
use App\Domain\Subscription\Services\SubscriptionService;
use App\Exceptions\Custom\TokenNotFoundException;

class ConfirmSubscriptionCommand
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService
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
