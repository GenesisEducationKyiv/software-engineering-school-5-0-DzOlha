<?php

namespace App\Application\Subscription\Utils;

use App\Domain\Subscription\Entities\Subscription;

interface SubscriptionUrlBuilderInterface
{
    public function buildConfirmationUrl(Subscription $subscription): ?string;

    public function buildUnsubscribeUrl(Subscription $subscription): ?string;
}
