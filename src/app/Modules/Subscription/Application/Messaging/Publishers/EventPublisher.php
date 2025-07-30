<?php

namespace App\Modules\Subscription\Application\Messaging\Publishers;

use App\Modules\Observability\Presentation\Interface\ObservabilityModuleInterface;
use App\Modules\Subscription\Application\Messaging\Brokers\MessageBrokerInterface;
use App\Modules\Subscription\Application\Messaging\Events\EventInterface;
use App\Modules\Subscription\Application\Messaging\Generator\EventKeyGeneratorInterface;
use App\Modules\Subscription\Application\Messaging\Messages\EventMessage;
use App\Modules\Subscription\Application\Messaging\Routing\RoutingStrategyInterface;

readonly class EventPublisher implements EventPublisherInterface
{
    public function __construct(
        private MessageBrokerInterface $broker,
        private RoutingStrategyInterface $routingStrategy,
        private EventKeyGeneratorInterface $keyGenerator,
        private ObservabilityModuleInterface $monitor
    ) {
    }

    public function publish(EventInterface $event): void
    {
        $message = new EventMessage($event, $this->routingStrategy, $this->keyGenerator);

        $this->broker->publish(
            $message->getExchange(),
            $message->getRoutingKey(),
            $message->toArray(),
            $message->getHeaders()
        );

        $this->monitor->logger()->logInfo(
            "Published event into the message broker",
            [
                'module' => 'Subscription',
                'exchange' => $message->getExchange(),
                'routingKey' => $message->getRoutingKey(),
                'message' => $message->toArray()
            ]
        );
    }
}
