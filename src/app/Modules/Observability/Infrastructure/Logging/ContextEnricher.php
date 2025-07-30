<?php

namespace App\Modules\Observability\Infrastructure\Logging;

class ContextEnricher
{
    /**
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    public static function enrich(array $context = []): array
    {
        $defaults = [
            'trace_id'   => request()->header('X-Trace-Id') ?? uniqid('trace_', true),
            'user_id'    => auth()->id() ?? 'guest',
            'request_id' => request()->header('X-Request-Id') ?? uniqid('req_'),
            'environment' => config('app.env'),
            'timestamp'  => gmdate('c'), // ISO8601 UTC timestamp
        ];

        return array_merge($defaults, $context);
    }
}
