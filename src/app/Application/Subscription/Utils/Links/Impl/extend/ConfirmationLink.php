<?php

namespace App\Application\Subscription\Utils\Links\Impl\extend;

use App\Application\Subscription\Utils\Links\Impl\AbstractSubscriptionLink;

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
