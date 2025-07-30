<?php

namespace App\Modules\Observability\Infrastructure\Services;

use App\Modules\Observability\Application\Logging\LoggingServiceInterface;
use App\Modules\Observability\Application\Metrics\Collector\MetricsCollectorInterface;
use App\Modules\Observability\Application\Services\ObservabilityServiceInterface;

class ObservabilityService implements ObservabilityServiceInterface
{
    public function __construct(
        private readonly LoggingServiceInterface $logger,
        private readonly MetricsCollectorInterface $collector
    ) {
    }

    public function logger(): LoggingServiceInterface
    {
        return $this->logger;
    }

    public function metrics(): MetricsCollectorInterface
    {
        return $this->collector;
    }
}
