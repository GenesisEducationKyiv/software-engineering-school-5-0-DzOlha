<?php

namespace App\Modules\Notification\Application\Messaging\Executors;

use App\Modules\Notification\Application\Messaging\Messages\EventBodyMessage;

interface EventHandlerExecutorInterface
{
    public function execute(string $handlerClass, EventBodyMessage $eventData): bool;
}
