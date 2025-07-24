<?php

namespace App\Modules\Notification\Infrastructure\Messaging\Executors;

use App\Modules\Notification\Application\Messaging\Executors\EventHandlerExecutorInterface;
use App\Modules\Notification\Application\Messaging\Messages\EventBodyMessage;
use App\Modules\Notification\Application\Repositories\ProcessedEventsRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;

readonly class EventHandlerExecutor implements EventHandlerExecutorInterface
{
    public function __construct(
        private Container $container,
        private ProcessedEventsRepositoryInterface $processedEventsRepository
    ) {
    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(string $handlerClass, EventBodyMessage $eventData): bool
    {
        $eventKey = $eventData->getEventKey();
        $eventType = $eventData->getEventType();

        if ($this->processedEventsRepository->isProcessed($eventKey)) {
            return true;
        }

        /**
         * @var object $handler
         */
        $handler = $this->container->make($handlerClass);

        if (!method_exists($handler, 'handle')) {
            throw new \InvalidArgumentException(
                "Handler {$handlerClass} must have a handle method"
            );
        }

        $result = $handler->handle($eventData);

        if ($result !== false) {
            $this->processedEventsRepository->markAsProcessed($eventKey, class_basename($eventType));
        }

        return $result !== false;
    }
}
