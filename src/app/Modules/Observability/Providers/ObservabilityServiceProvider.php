<?php

namespace App\Modules\Observability\Providers;

use App\Modules\Observability\Application\Logging\LoggingServiceInterface;
use App\Modules\Observability\Application\Metrics\Collector\MetricsCollector;
use App\Modules\Observability\Application\Metrics\Collector\MetricsCollectorInterface;
use App\Modules\Observability\Application\Metrics\MetricsServiceInterface;
use App\Modules\Observability\Domain\Sampling\ConfigBasedSamplingStrategy;
use App\Modules\Observability\Domain\Sampling\LogSamplingStrategyInterface;
use App\Modules\Observability\Infrastructure\Logging\LokiLogger;
use App\Modules\Observability\Infrastructure\Metrics\OpenTelemetryMetricsExporter;
use App\Modules\Observability\Presentation\Interface\ObservabilityModule;
use App\Modules\Observability\Presentation\Interface\ObservabilityModuleInterface;
use Illuminate\Support\ServiceProvider;

class ObservabilityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            LogSamplingStrategyInterface::class,
            ConfigBasedSamplingStrategy::class
        );

        $this->app->singleton(LoggingServiceInterface::class, LokiLogger::class);

        $this->app->singleton(MetricsServiceInterface::class, function () {
            /**
             * @var string $endpoint
             */
            $endpoint = config('observability.otel.metrics_endpoint');
            return new OpenTelemetryMetricsExporter($endpoint);
        });

        $this->app->singleton(MetricsCollectorInterface::class, MetricsCollector::class);
        $this->app->singleton(ObservabilityModuleInterface::class, ObservabilityModule::class);
    }

    public function boot(): void
    {
    }
}
