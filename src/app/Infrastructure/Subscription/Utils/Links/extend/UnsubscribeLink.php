<?php

namespace App\Infrastructure\Subscription\Utils\Links\extend;

use App\Infrastructure\Subscription\Utils\Links\AbstractSubscriptionLink;

class UnsubscribeLink extends AbstractSubscriptionLink
{
    public function getToken(): ?string
    {
        return $this->subscription->getUnsubscribeToken()?->getValue();
    }

    public function getEndpoint(): string
    {
        return '/unsubscribe';
    }
}
