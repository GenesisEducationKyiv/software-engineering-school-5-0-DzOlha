<?php

namespace App\Modules\Email\Application\Utils\Links\Implementation\Concrete;

use App\Modules\Email\Application\Utils\Links\Implementation\AbstractSubscriptionLink;

class ConfirmationLink extends AbstractSubscriptionLink
{
    public function getToken(): ?string
    {
        return $this->subscription->getConfirmationToken();
    }

    public function getEndpoint(): string
    {
        return '/confirm';
    }
}
