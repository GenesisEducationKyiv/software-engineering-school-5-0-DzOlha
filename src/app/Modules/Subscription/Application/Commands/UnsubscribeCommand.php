<?php

namespace App\Modules\Subscription\Application\Commands;

use App\Exceptions\Custom\TokenNotFoundException;
use App\Modules\Subscription\Application\DTOs\UnsubscribeRequestDTO;
use App\Modules\Subscription\Application\Services\SubscriptionServiceInterface;

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
