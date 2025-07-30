<?php

namespace App\Modules\Weather\Infrastructure\Repositories\Cache\Monitor;

use App\Modules\Observability\Presentation\Interface\ObservabilityModuleInterface;
use App\Modules\Weather\Domain\Repositories\Cache\Monitor\WeatherCacheMonitorInterface;
use Prometheus\CollectorRegistry;
use Prometheus\Exception\MetricsRegistrationException;

class PrometheusWeatherCacheMonitor implements WeatherCacheMonitorInterface
{
    private const CACHE_NAMESPACE = 'weather_cache';
    private const HIT_TOTAL = 'hit_total';
    private const MISS_TOTAL = 'miss_total';

    public function __construct(
        private readonly CollectorRegistry $registry,
        private readonly ObservabilityModuleInterface $monitor
    ) {
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function incrementHit(string $location, string $type): void
    {
        $counter = $this->registry->getOrRegisterCounter(
            self::CACHE_NAMESPACE,
            self::HIT_TOTAL,
            'Number of weather cache hits',
            ['location', 'type']
        );
        $counter->inc([$location, $type]);

        $this->monitor->metrics()->incrementCacheHits(self::HIT_TOTAL);
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function incrementMiss(string $location, string $type): void
    {
        $counter = $this->registry->getOrRegisterCounter(
            self::CACHE_NAMESPACE,
            self::MISS_TOTAL,
            'Number of weather cache misses',
            ['location', 'type']
        );
        $counter->inc([$location, $type]);

        $this->monitor->metrics()->incrementCacheMisses(self::MISS_TOTAL);
    }
}
