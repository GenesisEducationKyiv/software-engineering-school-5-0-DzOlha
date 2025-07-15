<?php

namespace App\Modules\Email\Application\Utils\Links\Implementation\Concrete;

use App\Modules\Email\Application\Utils\Links\Implementation\AbstractSubscriptionLink;

class UnsubscribeLink extends AbstractSubscriptionLink
{
    public function getToken(): ?string
    {
        return $this->subscription->getUnsubscribeToken();
    }

    public function getEndpoint(): string
    {
        return '/unsubscribe';
    }
}
