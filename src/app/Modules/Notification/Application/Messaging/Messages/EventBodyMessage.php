<?php

namespace App\Modules\Notification\Application\Messaging\Messages;

readonly class EventBodyMessage
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

    /**
     * @return array<string, mixed>
     * @throws \JsonException
     */
    public function toArray(): array
    {
        return [
            'event_key' => $this->eventKey,
            'event_type' => $this->eventType,
            'payload' => json_encode($this->payload, JSON_THROW_ON_ERROR)
        ];
    }
}
