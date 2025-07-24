<?php

namespace App\Modules\Notification\Application\Messaging\Handlers\Registry;

class ConfigurationEventHandlerRegistry implements EventHandlerRegistryInterface
{
    /**
     * @var array<string, string[]>
     */
    private array $handlers = [];

    /**
     * @param mixed $configuration
     */
    public function __construct(mixed $configuration)
    {
        /**
         * @var array<string, array<string, string>> $configuration
         */
        $this->loadConfiguration($configuration);
    }

    /**
     * @param array<string, array<string, string>> $config
     * @return void
     */
    private function loadConfiguration(array $config): void
    {
        foreach ($config as $eventType => $handlerClasses) {
            foreach ((array) $handlerClasses as $handlerClass) {
                $this->register($eventType, $handlerClass);
            }
        }
    }

    public function register(string $eventType, string $handlerClass): void
    {
        if (!isset($this->handlers[$eventType])) {
            $this->handlers[$eventType] = [];
        }
        $this->handlers[$eventType][] = $handlerClass;
    }

    /**
     * @param string $eventType
     * @return string[]
     */
    public function getHandlers(string $eventType): array
    {
        return $this->handlers[$eventType] ?? [];
    }

    public function hasHandlers(string $eventType): bool
    {
        return !empty($this->handlers[$eventType]);
    }
}
