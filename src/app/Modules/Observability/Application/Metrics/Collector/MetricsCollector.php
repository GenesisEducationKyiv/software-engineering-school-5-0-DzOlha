<?php

namespace App\Modules\Observability\Application\Metrics\Collector;

use App\Modules\Observability\Application\Metrics\MetricsServiceInterface;

readonly class MetricsCollector implements MetricsCollectorInterface
{
    public function __construct(
        private MetricsServiceInterface $metrics
    ) {
    }

    public function export(): void
    {
        $this->metrics->export();
    }

    // RED Metrics - Rate
    public function incrementHttpRequests(string $method, string $endpoint, int $statusCode): void
    {
        $this->metrics->counter('http_requests_total', 1, [
            'method' => $method,
            'endpoint' => $endpoint,
            'status_code' => (string) $statusCode,
        ]);
    }

    public function incrementApiRequests(string $endpoint, string $method): void
    {
        $this->metrics->counter('api_requests_total', 1, [
            'endpoint' => $endpoint,
            'method' => $method,
        ]);
    }

    // RED Metrics - Errors
    public function incrementHttpErrors(
        string $method,
        string $endpoint,
        int $statusCode,
        string $errorType = 'http_error'
    ): void {
        $this->metrics->counter('http_errors_total', 1, [
            'method' => $method,
            'endpoint' => $endpoint,
            'status_code' => (string) $statusCode,
            'error_type' => $errorType,
        ]);
    }

    public function incrementExternalServiceErrors(string $provider, string $errorType): void
    {
        $this->metrics->counter('external_service_errors_total', 1, [
            'provider' => $provider,
            'error_type' => $errorType,
        ]);
    }

    public function incrementApplicationErrors(
        string $errorType,
        string $module = 'unknown'
    ): void {
        $this->metrics->counter('application_errors_total', 1, [
            'error_type' => $errorType,
            'module' => $module,
        ]);
    }

    // RED Metrics - Duration
    public function recordHttpRequestDuration(
        float $duration,
        string $method,
        string $endpoint,
        int $statusCode
    ): void {
        $this->metrics->histogram('http_request_duration_seconds', $duration, [
            'method' => $method,
            'endpoint' => $endpoint,
            'status_code' => (string) $statusCode,
        ]);
    }

    public function recordDatabaseQueryDuration(
        float $duration,
        string $queryType = 'unknown'
    ): void {
        $this->metrics->histogram('db_query_duration_seconds', $duration, [
            'query_type' => $queryType,
        ]);
    }

    public function recordExternalApiDuration(
        float $duration,
        string $provider,
        string $endpoint
    ): void {
        $this->metrics->histogram('external_api_duration_seconds', $duration, [
            'provider' => $provider,
            'endpoint' => $endpoint,
        ]);
    }

    // USE Metrics - Utilization
    public function recordMemoryUsage(float $bytes): void
    {
        $this->metrics->gauge('memory_usage_bytes', $bytes);
    }

    public function recordDatabaseConnections(int $activeConnections): void
    {
        $this->metrics->gauge('db_connections_active', $activeConnections);
    }

    public function recordQueueSize(int $size, string $queueName): void
    {
        $this->metrics->gauge('queue_size', $size, [
            'queue_name' => $queueName,
        ]);
    }

    // USE Metrics - Saturation
    public function recordQueueBacklog(float $seconds, string $queueName): void
    {
        $this->metrics->gauge('queue_backlog_seconds', $seconds, [
            'queue_name' => $queueName,
        ]);
    }

    public function recordConnectionPoolUtilization(float $ratio): void
    {
        $this->metrics->gauge('db_connection_pool_utilization_ratio', $ratio);
    }

    public function incrementRateLimitHits(string $endpoint, string $identifier): void
    {
        $this->metrics->counter('rate_limit_hits_total', 1, [
            'endpoint' => $endpoint,
            'identifier' => $identifier,
        ]);
    }

    // USE Metrics - Errors (Resource-level)
    public function incrementDatabaseConnectionErrors(string $errorType): void
    {
        $this->metrics->counter('db_connection_errors_total', 1, [
            'error_type' => $errorType,
        ]);
    }

    public function incrementQueueProcessingErrors(string $queueName, string $jobClass): void
    {
        $this->metrics->counter('queue_processing_errors_total', 1, [
            'queue_name' => $queueName,
            'job_class' => $jobClass,
        ]);
    }

    public function incrementCacheErrors(string $operation, string $errorType): void
    {
        $this->metrics->counter('cache_errors_total', 1, [
            'operation' => $operation,
            'error_type' => $errorType,
        ]);
    }

    // Business Metrics
    public function incrementWeatherFetches(string $provider, string $city, bool $success): void
    {
        $this->metrics->counter('weather_fetches_total', 1, [
            'provider' => $provider,
            'city' => $city,
            'success' => $success ? 'true' : 'false',
        ]);
    }

    public function incrementEmailSubscriptions(string $email, bool $success): void
    {
        $this->metrics->counter('email_subscriptions_total', 1, [
            'success' => $success ? 'true' : 'false',
        ]);
    }

    public function incrementWeatherUpdates(string $city): void
    {
        $this->metrics->counter('weather_updates_total', 1, [
            'city' => $city,
        ]);
    }

    // Performance Metrics
    public function incrementCacheHits(string $key): void
    {
        $this->metrics->counter('cache_hits_total', 1, [
            'cache_key' => $key,
        ]);
    }

    public function incrementCacheMisses(string $key): void
    {
        $this->metrics->counter('cache_misses_total', 1, [
            'cache_key' => $key,
        ]);
    }

    public function recordProviderResponseTime(
        float $duration,
        string $provider,
        string $endpoint
    ): void {
        $this->metrics->histogram('weather_provider_response_seconds', $duration, [
            'provider' => $provider,
            'endpoint' => $endpoint,
        ]);
    }
}
