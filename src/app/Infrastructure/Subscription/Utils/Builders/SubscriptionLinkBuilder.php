<?php

namespace App\Infrastructure\Subscription\Utils\Builders;

use App\Application\Subscription\Utils\Builders\SubscriptionLinkBuilderInterface;
use App\Application\Subscription\Utils\Links\SubscriptionLinkInterface;
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
