<?php

namespace App\Modules\Subscription\Application\Commands;

use App\Exceptions\Custom\TokenNotFoundException;
use App\Modules\Subscription\Application\DTOs\ConfirmSubscriptionRequestDTO;
use App\Modules\Subscription\Application\Services\SubscriptionServiceInterface;

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
