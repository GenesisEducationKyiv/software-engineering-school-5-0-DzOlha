<?php

namespace App\Domain\Weather\Repositories\Cache\Monitor;

interface WeatherCacheMonitorInterface
{
    public function incrementHit(string $location, string $type): void;
    public function incrementMiss(string $location, string $type): void;
}
