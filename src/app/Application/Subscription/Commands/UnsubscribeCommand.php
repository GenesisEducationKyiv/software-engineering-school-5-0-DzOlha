<?php

namespace App\Application\Subscription\Commands;

use App\Application\Subscription\DTOs\UnsubscribeRequestDTO;
use App\Application\Subscription\Services\SubscriptionServiceInterface;
use App\Exceptions\Custom\TokenNotFoundException;

class UnsubscribeCommand
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptionService
    ) {
    }

    /**
     * @throws TokenNotFoundException
     */
    public function execute(UnsubscribeRequestDTO $dto): bool
    {
        $subscription = $this->subscriptionService->unsubscribe($dto);
        return $subscription !== null;
    }
}
