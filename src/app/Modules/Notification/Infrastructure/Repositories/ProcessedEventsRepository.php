<?php

namespace App\Modules\Notification\Infrastructure\Repositories;

use App\Modules\Notification\Application\Repositories\ProcessedEventsRepositoryInterface;
use App\Modules\Notification\Infrastructure\Models\ProcessedEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessedEventsRepository implements ProcessedEventsRepositoryInterface
{
    public function isProcessed(string $eventKey): bool
    {
        $table = ProcessedEvent::getTableName();

        return DB::table($table)
            ->where('event_key', $eventKey)
            ->where('status', 1)
            ->exists();
    }

    public function markAsProcessed(string $eventKey, string $eventType): bool
    {
        $table = ProcessedEvent::getTableName();

        return DB::table($table)->insert([
            'event_key'   => $eventKey,
            'event_name'  => $eventType,
            'status'      => 1,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);
    }
}
