<?php

namespace App\Modules\Notification\Application\Messaging\Consumers;

use App\Modules\Notification\Application\Messaging\Executors\EventHandlerExecutorInterface;
use App\Modules\Notification\Application\Messaging\Handlers\Registry\EventHandlerRegistryInterface;
use App\Modules\Notification\Application\Messaging\Messages\EventBodyMessage;
use App\Modules\Subscription\Application\Messaging\Brokers\MessageBrokerInterface;
use Illuminate\Support\Facades\Log;

class EventConsumer implements EventConsumerInterface
{
    private MessageBrokerInterface $broker;
    private EventHandlerRegistryInterface $registry;
    private EventHandlerExecutorInterface $executor;

    public function __construct(
        MessageBrokerInterface $broker,
        EventHandlerRegistryInterface $registry,
        EventHandlerExecutorInterface $executor
    ) {
        $this->broker = $broker;
        $this->registry = $registry;
        $this->executor = $executor;
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

        Log::info('Consuming event');

        if (!$this->registry->hasHandlers($eventType)) {
            Log::info(
                'No handlers registered for event type',
                ['event_type' => $eventType]
            );
            return true;
        }

        $handlers = $this->registry->getHandlers($eventType);
        $allSucceeded = true;

        foreach ($handlers as $handlerClass) {
            try {
                $success = $this->executor->execute($handlerClass, $message);
                $allSucceeded = $allSucceeded && $success;

                Log::info('Event handler executed', [
                    'handler' => $handlerClass,
                    'event_type' => $eventType,
                    'success' => $success,
                ]);
            } catch (\Exception $e) {
                Log::error('Event handler failed', [
                    'handler' => $handlerClass,
                    'event_type' => $eventType,
                    'error' => $e->getMessage(),
                ]);
                $allSucceeded = false;
            }
        }

        return $allSucceeded;
    }
}
