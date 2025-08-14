<?php

namespace App\Modules\Subscription\Application\Messaging\Publishers;

use App\Modules\Subscription\Application\Messaging\Events\EventInterface;

interface EventPublisherInterface
{
    public function publish(EventInterface $event): void;
}
