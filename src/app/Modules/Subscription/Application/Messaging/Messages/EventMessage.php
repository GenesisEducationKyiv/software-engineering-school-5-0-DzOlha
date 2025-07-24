<?php

namespace App\Modules\Subscription\Application\Messaging\Messages;

use App\Modules\Subscription\Application\Messaging\Events\EventInterface;
use App\Modules\Subscription\Application\Messaging\Generator\EventKeyGeneratorInterface;
use App\Modules\Subscription\Application\Messaging\Routing\RoutingStrategyInterface;

readonly class EventMessage implements MessageInterface
{
    public function __construct(
        private EventInterface $event,
        private RoutingStrategyInterface $routingStrategy,
        private EventKeyGeneratorInterface $keyGenerator
    )
    {
    }

    public function getRoutingKey(): string
    {
        return $this->routingStrategy->getRoutingKey($this->event);
    }

    public function getExchange(): string
    {
        return $this->routingStrategy->getExchange($this->event);
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return [
            'event_type' => get_class($this->event),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'event_key' => $this->keyGenerator->generateUniqueKey(),
            'event_type' => get_class($this->event),
            'payload' => $this->event->toArray()
        ];
    }
}
