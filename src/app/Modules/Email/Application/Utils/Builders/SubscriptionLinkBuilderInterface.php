<?php

namespace App\Application\Subscription\Utils\Builders;

use App\Application\Subscription\Utils\Links\Inter\SubscriptionLinkInterface;

interface SubscriptionLinkBuilderInterface
{
    public function build(SubscriptionLinkInterface $context): ?string;
}
