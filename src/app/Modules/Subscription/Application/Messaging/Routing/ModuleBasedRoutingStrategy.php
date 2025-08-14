<?php

namespace App\Modules\Subscription\Application\Messaging\Routing;

use App\Modules\Subscription\Application\Messaging\Events\EventInterface;

class ModuleBasedRoutingStrategy implements RoutingStrategyInterface
{
    public function getExchange(EventInterface $event): string
    {
        $module = $this->getModuleName($event);

        return "{$module}.events";
    }

    public function getRoutingKey(EventInterface $event): string
    {
        $eventClass = get_class($event);
        $module = $this->getModuleName($event);
        $eventName = $this->camelToSnake(class_basename($eventClass));

        return "{$module}.{$eventName}";
    }

    private function camelToSnake(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input) ?? $input);
    }

    private function getModuleName(EventInterface $event): string
    {
        $eventClass = get_class($event);
        $parts = explode('\\', $eventClass);

        return strtolower($parts[2] ?? 'default');
    }
}
