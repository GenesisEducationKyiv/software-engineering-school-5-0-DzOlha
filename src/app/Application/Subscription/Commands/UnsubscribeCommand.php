<?php

namespace App\Application\Subscription\Commands;

use App\Application\Subscription\DTOs\UnsubscribeRequestDTO;
use App\Domain\Subscription\Services\SubscriptionService;
use App\Exceptions\Custom\TokenNotFoundException;

class UnsubscribeCommand
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService
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
