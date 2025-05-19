<?php

namespace App\Domain\Subscription\Repositories;

use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Subscription\Entities\Subscription as SubscriptionEntity;
use App\Domain\Subscription\ValueObjects\Token;
use App\Exceptions\Custom\FrequencyNotFoundException;
use App\Infrastructure\Subscription\Models\Subscription as SubscriptionModel;

interface SubscriptionRepositoryInterface
{
    /**
     * @param SubscriptionEntity $subscriptionEntity
     * @return SubscriptionEntity
     * @throws FrequencyNotFoundException
     */
    public function save(Subscription $subscriptionEntity): Subscription;

    public function hasActiveSubscription(Subscription $subscription): bool;
    public function findSubscriptionById(int $id): ?Subscription;
    public function getPendingSubscription(Subscription $subscription): ?SubscriptionModel;
    public function hasValidConfirmationToken(int $subscriptionId): bool;
    public function replaceTokensForPending(int $subscriptionId, Token $confirm, Token $cancel): void;
    public function delete(int $subscriptionId): bool;
    public function confirmSubscriptionByToken(string $token): ?Subscription;
    public function unsubscribeByToken(string $token): ?Subscription;
}
