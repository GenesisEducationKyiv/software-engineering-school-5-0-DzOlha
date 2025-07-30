<?php

namespace Tests\Unit\Modules\Weather\Infrastructure\Repositories\Cache\Monitor;

use App\Modules\Observability\Application\Metrics\Collector\MetricsCollectorInterface;
use App\Modules\Observability\Presentation\Interface\ObservabilityModuleInterface;
use App\Modules\Weather\Infrastructure\Repositories\Cache\Monitor\PrometheusWeatherCacheMonitor;
use Mockery;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Prometheus\Exception\MetricsRegistrationException;
use Tests\TestCase;

class PrometheusWeatherCacheMonitorTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function test_it_increments_hit_counter(): void
    {
        $location = 'Lviv';
        $type = 'current';

        $counterMock = Mockery::mock(Counter::class);
        $counterMock->shouldReceive('inc')
            ->once()
            ->with([$location, $type]);

        $registryMock = Mockery::mock(CollectorRegistry::class);
        $registryMock->shouldReceive('getOrRegisterCounter')
            ->once()
            ->with(
                'weather_cache',
                'hit_total',
                'Number of weather cache hits',
                ['location', 'type']
            )
            ->andReturn($counterMock);

        $metricsMock = Mockery::mock(MetricsCollectorInterface::class);
        $metricsMock->shouldReceive('incrementCacheHits')
            ->once()
            ->with('hit_total');

        $observabilityMock = Mockery::mock(ObservabilityModuleInterface::class);
        $observabilityMock->shouldReceive('metrics')
            ->once()
            ->andReturn($metricsMock);

        $monitor = new PrometheusWeatherCacheMonitor(
            $registryMock,
            $observabilityMock
        );
        $monitor->incrementHit($location, $type);
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function test_it_increments_miss_counter(): void
    {
        $location = 'Kyiv';
        $type = 'forecast';

        $counterMock = Mockery::mock(Counter::class);
        $counterMock->shouldReceive('inc')
            ->once()
            ->with([$location, $type]);

        $registryMock = Mockery::mock(CollectorRegistry::class);
        $registryMock->shouldReceive('getOrRegisterCounter')
            ->once()
            ->with(
                'weather_cache',
                'miss_total',
                'Number of weather cache misses',
                ['location', 'type']
            )
            ->andReturn($counterMock);

        $metricsMock = Mockery::mock(MetricsCollectorInterface::class);
        $metricsMock->shouldReceive('incrementCacheMisses')
            ->once()
            ->with('miss_total');

        $observabilityMock = Mockery::mock(ObservabilityModuleInterface::class);
        $observabilityMock->shouldReceive('metrics')
            ->once()
            ->andReturn($metricsMock);

        $monitor = new PrometheusWeatherCacheMonitor(
            $registryMock,
            $observabilityMock
        );
        $monitor->incrementMiss($location, $type);
    }
}
