<?php

namespace App\Infrastructure\Subscription\Utils\Links;

use App\Application\Subscription\Utils\Links\SubscriptionLinkInterface;
use App\Domain\Subscription\Entities\Subscription;

abstract class AbstractSubscriptionLink implements SubscriptionLinkInterface
{
    public function __construct(
        protected readonly Subscription $subscription,
    ) {
    }

    abstract public function getEndpoint(): string;
    abstract public function getToken(): ?string;
}
