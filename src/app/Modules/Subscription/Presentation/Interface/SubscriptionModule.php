<?php

namespace App\Modules\Subscription\Presentation\Interface;

use App\Modules\Subscription\Domain\Entities\Subscription;
use App\Modules\Subscription\Domain\Repositories\SubscriptionRepositoryInterface;

readonly class SubscriptionModule implements SubscriptionModuleInterface
{
    public function __construct(
        private SubscriptionRepositoryInterface $repository
    ) {
    }
    public function findSubscriptionEntityById(int $id): ?Subscription
    {
        return $this->repository->findSubscriptionById($id);
    }

    public function updateSubscriptionEmailStatus(
        int $subscriptionId,
        int $intervalMinutes,
        bool $success = true
    ): bool {
        return $this->repository->updateSubscriptionEmailStatus(
            $subscriptionId,
            $intervalMinutes,
            $success
        );
    }

    public function deleteSubscription(int $subscriptionId): bool
    {
        return $this->repository->delete($subscriptionId);
    }
}
