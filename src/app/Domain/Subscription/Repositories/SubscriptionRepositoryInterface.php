<?php

namespace App\Domain\Subscription\Repositories;

use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Subscription\ValueObjects\Token\Token;
use App\Exceptions\Custom\FrequencyNotFoundException;

interface SubscriptionRepositoryInterface
{
    /**
     * @param Subscription $entity
     * @return Subscription
     * @throws FrequencyNotFoundException
     */
    public function save(Subscription $entity): Subscription;

    public function hasActiveSubscription(Subscription $entity): bool;

    public function findSubscriptionById(int $id): ?Subscription;

    public function getPendingSubscriptionId(Subscription $entity): ?int;

    public function hasValidConfirmationToken(int $subscriptionId): bool;

    public function replaceTokensForPending(int $subscriptionId, Token $confirm, Token $cancel): void;

    public function delete(int $subscriptionId): bool;

    public function confirmSubscriptionByToken(string $token): ?Subscription;

    public function unsubscribeByToken(string $token): ?Subscription;

    public function updateSubscriptionEmailStatus(
        int $subscriptionId,
        int $intervalMinutes,
        bool $success = true
    ): bool;

    public function expireConfirmationToken(int $subscriptionId): bool;
}
