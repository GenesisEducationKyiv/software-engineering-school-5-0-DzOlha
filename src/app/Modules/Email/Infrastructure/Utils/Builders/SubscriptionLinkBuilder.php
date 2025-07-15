<?php

namespace App\Modules\Email\Infrastructure\Utils\Builders;

use App\Modules\Email\Application\Utils\Builders\SubscriptionLinkBuilderInterface;
use App\Modules\Email\Application\Utils\Links\Interface\SubscriptionLinkInterface;
use Illuminate\Support\Facades\URL;

class SubscriptionLinkBuilder implements SubscriptionLinkBuilderInterface
{
    public function build(SubscriptionLinkInterface $context): ?string
    {
        $token = $context->getToken();

        return $token
            ? URL::to("{$context->getEndpoint()}?token={$token}")
            : null;
    }
}
