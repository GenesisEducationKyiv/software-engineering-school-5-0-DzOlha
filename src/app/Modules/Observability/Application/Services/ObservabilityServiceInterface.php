<?php

namespace App\Modules\Observability\Application\Services;

use App\Modules\Observability\Application\Logging\LoggingServiceInterface;
use App\Modules\Observability\Application\Metrics\Collector\MetricsCollectorInterface;

interface ObservabilityServiceInterface
{
    public function logger(): LoggingServiceInterface;
    public function metrics(): MetricsCollectorInterface;
}
