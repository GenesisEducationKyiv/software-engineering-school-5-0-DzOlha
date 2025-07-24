<?php

namespace App\Modules\Notification\Application\Messaging\Handlers\Registry;

interface EventHandlerRegistryInterface
{
    public function register(string $eventType, string $handlerClass): void;

    /**
     * @param string $eventType
     * @return string[]
     */
    public function getHandlers(string $eventType): array;
    public function hasHandlers(string $eventType): bool;
}
