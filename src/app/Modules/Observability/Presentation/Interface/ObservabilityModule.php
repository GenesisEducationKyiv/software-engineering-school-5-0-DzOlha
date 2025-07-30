<?php

namespace App\Modules\Observability\Presentation\Interface;

use App\Modules\Observability\Application\Logging\LoggingServiceInterface;
use App\Modules\Observability\Application\Metrics\Collector\MetricsCollectorInterface;
use App\Modules\Observability\Application\Services\ObservabilityServiceInterface;

readonly class ObservabilityModule implements ObservabilityModuleInterface
{
    public function __construct(
        private ObservabilityServiceInterface $observabilityService
    ) {
    }

    public function logger(): LoggingServiceInterface
    {
        return $this->observabilityService->logger();
    }

    public function metrics(): MetricsCollectorInterface
    {
        return $this->observabilityService->metrics();
    }
}
