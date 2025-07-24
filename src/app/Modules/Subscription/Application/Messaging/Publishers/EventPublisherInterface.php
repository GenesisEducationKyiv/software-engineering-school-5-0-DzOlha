<?php

namespace App\Modules\Subscription\Application\Messaging\Publishers;

use App\Modules\Subscription\Application\Messaging\Events\SubscriptionEvent;

interface EventPublisherInterface
{
    public function publish(SubscriptionEvent $event): void;
}
