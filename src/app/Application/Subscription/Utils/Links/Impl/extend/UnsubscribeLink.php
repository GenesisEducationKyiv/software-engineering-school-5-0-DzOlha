<?php

namespace App\Application\Subscription\Utils\Links\Impl\extend;

use App\Application\Subscription\Utils\Links\Impl\AbstractSubscriptionLink;

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
