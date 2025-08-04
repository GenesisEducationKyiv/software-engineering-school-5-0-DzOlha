<?php

namespace App\Modules\Notification\Application\Messaging\Messages;

readonly class MessageBody
{
    /**
     * @param string $eventKey
     * @param string $eventType
     * @param array<string, mixed> $payload
     */
    public function __construct(
        private string $eventKey,
        private string $eventType,
        private array $payload
    ) {
    }

    public function getEventKey(): string
    {
        return $this->eventKey;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
