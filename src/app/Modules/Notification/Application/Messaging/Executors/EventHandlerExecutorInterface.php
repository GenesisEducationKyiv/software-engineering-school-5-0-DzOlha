<?php

namespace App\Modules\Notification\Application\Messaging\Executors;

use App\Modules\Notification\Application\Messaging\Messages\MessageBody;

interface EventHandlerExecutorInterface
{
    public function execute(string $handlerClass, MessageBody $eventData): bool;
}
