<?php

return [
    'logging' => [
        'sampling' => [
            'DEBUG' => 0.1,
            'INFO'  => 0.5,
            'WARNING'  => 0.75,
            'ERROR' => 1.0,
        ],
        'loki_path' => storage_path('logs/loki.log')
    ],
    'metrics' => [
        'enabled'  => env('METRICS_ENABLED', true),
        'exporter' => 'otlp',
        'sampling_rate' => env('METRICS_SAMPLING_RATE', 1.0), // 100% by default
    ],
    'otel' => [
        'metrics_endpoint' => env('OTEL_METRICS_ENDPOINT', 'http://otel-collector:4318/v1/metrics'),
    ],
];
