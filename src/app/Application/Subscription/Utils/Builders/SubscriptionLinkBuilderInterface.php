<?php

namespace App\Infrastructure\Subscription\Utils\Builders;

use App\Infrastructure\Subscription\Utils\Links\Inter\SubscriptionLinkInterface;

interface SubscriptionLinkBuilderInterface
{
    public function build(SubscriptionLinkInterface $context): ?string;
}
