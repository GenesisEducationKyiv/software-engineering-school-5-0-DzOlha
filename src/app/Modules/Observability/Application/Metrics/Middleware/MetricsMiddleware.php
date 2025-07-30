<?php

namespace App\Modules\Observability\Application\Metrics\Middleware;

use App\Modules\Observability\Application\Metrics\Collector\MetricsCollectorInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class MetricsMiddleware
{
    public function __construct(
        private MetricsCollectorInterface $metricsCollector
    ) {
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var Response $processedRequest
         */
        $processedRequest = $next($request);
        if (!config('observability.metrics.enabled', true)) {
            return $processedRequest;
        }

        $startTime = microtime(true);

        $this->recordMetrics($request, $processedRequest, $startTime);

        $this->metricsCollector->export();

        return $processedRequest;
    }

    private function recordMetrics(Request $request, Response $response, float $startTime): void
    {
        $duration = microtime(true) - $startTime;
        $statusCode = $response->getStatusCode();
        $method = $request->getMethod();
        $path = $request->getPathInfo();
        $endpoint = $this->normalizeEndpoint($path);

        $this->metricsCollector->incrementHttpRequests($method, $endpoint, $statusCode);
        $this->metricsCollector->recordHttpRequestDuration($duration, $method, $endpoint, $statusCode);

        if ($statusCode >= 400) {
            $errorType = $this->getErrorType($statusCode);
            $this->metricsCollector->incrementHttpErrors($method, $endpoint, $statusCode, $errorType);
        }

        if (str_starts_with($endpoint, '/api/')) {
            $this->metricsCollector->incrementApiRequests($endpoint, $method);
        }

        if ($this->shouldRecordSample()) {
            $this->recordSystemMetrics();
        }
    }

    private function normalizeEndpoint(string $path): string
    {
        /**
         * @var string $path
         */
        $path = preg_replace('/\/\d+/', '/{id}', $path);

        if (strlen($path) > 100) {
            $path = substr($path, 0, 97) . '...';
        }

        return $path;
    }

    private function getErrorType(int $statusCode): string
    {
        return match (true) {
            $statusCode >= 500 => 'server_error',
            $statusCode >= 400 => 'client_error',
            default => 'unknown_error'
        };
    }

    private function recordSystemMetrics(): void
    {
        $this->metricsCollector->recordMemoryUsage(memory_get_usage(true));

        $this->metricsCollector->recordMemoryUsage(memory_get_peak_usage(true));

        if (app()->bound('db')) {
            try {
                $connections = app('db')->getConnections();
                $this->metricsCollector->recordDatabaseConnections(count($connections));
            } catch (\Exception $e) {
                return;
            }
        }
    }

    private function shouldRecordSample(): bool
    {
        $samplingPercent = config('observability.metrics.sampling_rate', 1.0);

        if (is_numeric($samplingPercent) && rand(1, 100) <= (int)$samplingPercent * 100) {
            return true;
        }

        return false;
    }
}
