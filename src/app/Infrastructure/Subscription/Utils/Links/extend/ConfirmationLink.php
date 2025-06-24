<?php

namespace App\Infrastructure\Subscription\Utils\Links\extend;

use App\Infrastructure\Subscription\Utils\Links\AbstractSubscriptionLink;

class ConfirmationLink extends AbstractSubscriptionLink
{
    public function getToken(): ?string
    {
        return $this->subscription->getConfirmationToken()?->getValue();
    }

    public function getEndpoint(): string
    {
        return '/confirm';
    }
}
