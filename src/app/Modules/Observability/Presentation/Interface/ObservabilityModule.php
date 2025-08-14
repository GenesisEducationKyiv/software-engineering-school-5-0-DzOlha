<?php

namespace App\Modules\Observability\Presentation\Interface;

use App\Modules\Observability\Application\Logging\LoggingServiceInterface;
use App\Modules\Observability\Application\Metrics\Collector\MetricsCollectorInterface;

readonly class ObservabilityModule implements ObservabilityModuleInterface
{
    public function __construct(
        private LoggingServiceInterface $logger,
        private MetricsCollectorInterface $metricsCollector
    ) {
    }

    public function logger(): LoggingServiceInterface
    {
        return $this->logger;
    }

    public function metrics(): MetricsCollectorInterface
    {
        return $this->metricsCollector;
    }
}
