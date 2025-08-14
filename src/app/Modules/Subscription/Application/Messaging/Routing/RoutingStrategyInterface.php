<?php

namespace App\Modules\Subscription\Application\Messaging\Routing;

use App\Modules\Subscription\Application\Messaging\Events\EventInterface;

interface RoutingStrategyInterface
{
    public function getExchange(EventInterface $event): string;
    public function getRoutingKey(EventInterface $event): string;
}
