<?php

namespace App\Modules\Notification\Application\Messaging\Handlers;

use App\Modules\Observability\Presentation\Interface\ObservabilityModuleInterface;
use App\Modules\Notification\Application\Messaging\Messages\MessageBody;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

abstract class EventHandler implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;

    public int $tries = 3;
    /**
     * @var int[]
     */
    public array $backoff = [10, 30, 60]; // Retry backoff in seconds
    public int $timeout = 300; // 5 minutes timeout

    public function __construct(
        protected readonly ObservabilityModuleInterface $monitor
    ) {
    }

    abstract public function handle(MessageBody $eventData): void;

    public function failed(\Throwable $exception): void
    {
        Log::error('Domain event handler failed', [
            'handler' => static::class,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
