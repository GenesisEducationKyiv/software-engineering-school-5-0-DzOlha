<?php

namespace App\Modules\Notification\Application\Messaging\Consumers;

use App\Modules\Notification\Application\Messaging\Executors\EventHandlerExecutorInterface;
use App\Modules\Notification\Application\Messaging\Handlers\Registry\EventHandlerRegistryInterface;
use App\Modules\Notification\Application\Messaging\Messages\EventBodyMessage;
use App\Modules\Observability\Presentation\Interface\ObservabilityModuleInterface;
use App\Modules\Subscription\Application\Messaging\Brokers\MessageBrokerInterface;

readonly class EventConsumer implements EventConsumerInterface
{
    public function __construct(
        private MessageBrokerInterface $broker,
        private EventHandlerRegistryInterface $registry,
        private EventHandlerExecutorInterface $executor,
        private ObservabilityModuleInterface $monitor
    ) {
    }

    public function consume(string $queue): void
    {
        $this->broker->consume(
            $queue,
            function (array $messageData) {
                /**
                 * @var array{
                 *       event_key: string,
                 *       event_type: string,
                 *       payload: array<string, mixed>
                 *  } $messageData
                 */
                return $this->handleMessage(
                    new EventBodyMessage(
                        $messageData['event_key'],
                        $messageData['event_type'],
                        $messageData['payload']
                    )
                );
            }
        );
    }

    /**
     * @throws \JsonException
     */
    private function handleMessage(EventBodyMessage $message): bool
    {
        $eventType = $message->getEventType();

        $this->monitor->logger()->logInfo('Consuming event', [
            'module' => 'Notification',
            'message' => $message->toArray(),
        ]);

        if (!$this->registry->hasHandlers($eventType)) {
            $this->monitor->logger()->logWarn(
                'No handlers registered for event type',
                [
                    'module' => 'Notification',
                    'message' => $message->toArray(),
                ]
            );
            return true;
        }

        $handlers = $this->registry->getHandlers($eventType);
        $allSucceeded = true;

        foreach ($handlers as $handlerClass) {
            try {
                $success = $this->executor->execute($handlerClass, $message);
                $allSucceeded = $allSucceeded && $success;

                $this->monitor->logger()->logInfo('Event handler executed', [
                    'module' => 'Notification',
                    'handler'    => $handlerClass,
                    'event_type' => $eventType,
                    'success'    => $success,
                ]);
            } catch (\Exception $e) {
                $this->monitor->logger()->logError('Event handler failed', [
                    'module' => 'Notification',
                    'handler'    => $handlerClass,
                    'event_type' => $eventType,
                    'error'      => $e->getMessage(),
                ]);
                $allSucceeded = false;
            }
        }

        return $allSucceeded;
    }
}
