<?php

namespace App\Modules\Subscription\Application\Messaging\Brokers;

interface MessageBrokerInterface
{
    /**
     * @param string $exchange
     * @param string $routingKey
     * @param array<string, mixed> $message
     * @param array<string, mixed> $headers
     * @return void
     */
    public function publish(
        string $exchange,
        string $routingKey,
        array $message,
        array $headers = []
    ): void;

    public function consume(string $queue, callable $callback): void;
}
