<?php

namespace App\Modules\Subscription\Application\Messaging\Messages;

interface MessageInterface
{
    public function getRoutingKey(): string;
    public function getExchange(): string;

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
