<?php

namespace App\Modules\Notification\Application\Messaging\Consumers;

interface EventConsumerInterface
{
    public function consume(string $queue): void;
}
