<?php

namespace App\Modules\Observability\Application\Metrics\Collector;

interface MetricsCollectorInterface
{
    public function export(): void;

    // RED Metrics - Rate
    public function incrementHttpRequests(string $method, string $endpoint, int $statusCode): void;

    public function incrementApiRequests(string $endpoint, string $method): void;

    // RED Metrics - Errors
    public function incrementHttpErrors(
        string $method,
        string $endpoint,
        int $statusCode,
        string $errorType = 'http_error'
    ): void;

    public function incrementExternalServiceErrors(string $provider, string $errorType): void;

    public function incrementApplicationErrors(string $errorType, string $module = 'unknown'): void;

    // RED Metrics - Duration
    public function recordHttpRequestDuration(
        float $duration,
        string $method,
        string $endpoint,
        int $statusCode
    ): void;

    public function recordDatabaseQueryDuration(float $duration, string $queryType = 'unknown'): void;

    public function recordExternalApiDuration(
        float $duration,
        string $provider,
        string $endpoint
    ): void;

    // USE Metrics - Utilization
    public function recordMemoryUsage(float $bytes): void;

    public function recordDatabaseConnections(int $activeConnections): void;

    public function recordQueueSize(int $size, string $queueName): void;

    // USE Metrics - Saturation
    public function recordQueueBacklog(float $seconds, string $queueName): void;

    public function recordConnectionPoolUtilization(float $ratio): void;

    public function incrementRateLimitHits(string $endpoint, string $identifier): void;

    // USE Metrics - Errors (Resource-level)
    public function incrementDatabaseConnectionErrors(string $errorType): void;

    public function incrementQueueProcessingErrors(string $queueName, string $jobClass): void;

    public function incrementCacheErrors(string $operation, string $errorType): void;

    // Business Metrics
    public function incrementWeatherFetches(string $provider, string $city, bool $success): void;

    public function incrementEmailSubscriptions(string $email, bool $success): void;

    public function incrementWeatherUpdates(string $city): void;

    // Performance Metrics
    public function incrementCacheHits(string $key): void;

    public function incrementCacheMisses(string $key): void;

    public function recordProviderResponseTime(float $duration, string $provider, string $endpoint): void;
}
