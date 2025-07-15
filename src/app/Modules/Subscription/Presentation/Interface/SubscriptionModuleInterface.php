<?php

namespace App\Modules\Subscription\Presentation\Interface;

use App\Modules\Subscription\Domain\Entities\Subscription;

interface SubscriptionModuleInterface
{
    public function findSubscriptionEntityById(int $id): ?Subscription;
    public function updateSubscriptionEmailStatus(
        int $subscriptionId,
        int $intervalMinutes,
        bool $success = true
    ): bool;

    public function deleteSubscription(int $subscriptionId): bool;
}
