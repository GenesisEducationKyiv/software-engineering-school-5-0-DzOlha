<?php

namespace App\Modules\Observability\Presentation\Interface;

use App\Modules\Observability\Application\Logging\LoggingServiceInterface;
use App\Modules\Observability\Application\Metrics\Collector\MetricsCollectorInterface;

interface ObservabilityModuleInterface
{
    public function logger(): LoggingServiceInterface;
    public function metrics(): MetricsCollectorInterface;
}
