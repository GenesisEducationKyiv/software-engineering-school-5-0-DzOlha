<?php

namespace App\Modules\Email\Application\Utils\Builders;

use App\Modules\Email\Application\Utils\Links\Interface\SubscriptionLinkInterface;

interface SubscriptionLinkBuilderInterface
{
    public function build(SubscriptionLinkInterface $context): ?string;
}
