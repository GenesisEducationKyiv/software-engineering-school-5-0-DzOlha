<?php

namespace App\Modules\Notification\Application\Repositories;

interface ProcessedEventsRepositoryInterface
{
    public function isProcessed(string $eventKey): bool;
    public function markAsProcessed(string $eventKey, string $eventType): bool;
}
