<?php

namespace App\Modules\Weather\Infrastructure\Repositories\Cache;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Modules\Weather\Domain\Repositories\Cache\Monitor\WeatherCacheMonitorInterface;
use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;
use App\Modules\Weather\Domain\ValueObjects\City\City;
use App\Modules\Weather\Domain\ValueObjects\WeatherData;
use Illuminate\Contracts\Cache\Repository as Cache;
use Psr\SimpleCache\InvalidArgumentException;

class WeatherRepositoryCache implements WeatherRepositoryInterface
{
    private const CACHE_TTL_MINUTES = 60;

    public function __construct(
        private readonly WeatherRepositoryInterface $realRepo,
        private readonly WeatherCacheMonitorInterface $monitor,
        private readonly Cache $cache
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws ApiAccessException
     * @throws CityNotFoundException
     */
    public function getCurrentWeather(City $city): WeatherData
    {
        return $this->cacheWrapper(
            $city,
            'get-current',
            fn(City $c) => $this->realRepo->getCurrentWeather($c)
        );
    }

    /**
     * @throws ApiAccessException
     * @throws InvalidArgumentException
     */
    public function cityExists(City $city): bool
    {
        return $this->cacheWrapper(
            $city,
            'city-exists',
            fn(City $c) => $this->realRepo->cityExists($c)
        );
    }

    private function makeCacheKey(City $city, string $suffix): string
    {
        return sprintf('weather:%s:%s', $city->getName(), $suffix);
    }

    /**
     * @template T
     * @param City $city
     * @param string $suffix
     * @param callable(City): T $callback
     * @return T
     * @throws InvalidArgumentException
     */
    private function cacheWrapper(City $city, string $suffix, callable $callback)
    {
        $key = $this->makeCacheKey($city, $suffix);

        $cached = $this->cache->get($key);
        if ($cached !== null) {
            $this->monitor->incrementHit($city->getName(), $suffix);
            return $cached;
        }

        $this->monitor->incrementMiss($city->getName(), $suffix);

        $result = $callback($city);

        $this->cache->put($key, $result, now()->addMinutes(self::CACHE_TTL_MINUTES));

        return $result;
    }
}
