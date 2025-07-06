<?php

namespace App\Application\Subscription\Utils\Links\Impl;

use App\Application\Subscription\Utils\Links\Inter\SubscriptionLinkInterface;
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
