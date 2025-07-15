<?php

namespace Tests\Unit\Modules\Weather\Infrastructure\Repositories\Cache;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Modules\Weather\Domain\Repositories\Cache\Monitor\WeatherCacheMonitorInterface;
use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;
use App\Modules\Weather\Domain\ValueObjects\City\City;
use App\Modules\Weather\Domain\ValueObjects\WeatherData;
use App\Modules\Weather\Infrastructure\Repositories\Cache\WeatherRepositoryCache;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Carbon;
use Mockery;
use Psr\SimpleCache\InvalidArgumentException;
use Tests\TestCase;

class WeatherRepositoryCacheTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @throws ApiAccessException
     * @throws CityNotFoundException
     * @throws InvalidArgumentException
     */
    public function test_get_current_weather_returns_cached_data(): void
    {
        $city = Mockery::mock(City::class);
        $city->shouldReceive('getName')->andReturn('Kyiv');

        $weatherData = Mockery::mock(WeatherData::class);

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('get')
            ->with('weather:Kyiv:get-current')
            ->once()
            ->andReturn($weatherData);

        $monitor = Mockery::mock(WeatherCacheMonitorInterface::class);
        $monitor->shouldReceive('incrementHit')
            ->with('Kyiv', 'get-current')
            ->once();

        $repo = Mockery::mock(WeatherRepositoryInterface::class);

        $cacheRepo = new WeatherRepositoryCache($repo, $monitor, $cache);
        $result = $cacheRepo->getCurrentWeather($city);

        $this->assertSame($weatherData, $result);
    }

    /**
     * @throws InvalidArgumentException
     * @throws ApiAccessException
     * @throws CityNotFoundException
     */
    public function test_get_current_weather_fetches_and_caches_if_not_found(): void
    {
        $city = Mockery::mock(City::class);
        $city->shouldReceive('getName')->andReturn('Lviv');

        $weatherData = Mockery::mock(WeatherData::class);

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('get')
            ->with('weather:Lviv:get-current')
            ->once()
            ->andReturn(null);

        $cache->shouldReceive('put')
            ->withArgs(function ($key, $value, $ttl) {
                return $key === 'weather:Lviv:get-current' && $value && $ttl instanceof \Illuminate\Support\Carbon;
            })
            ->once();

        $monitor = Mockery::mock(WeatherCacheMonitorInterface::class);
        $monitor->shouldReceive('incrementMiss')->with('Lviv', 'get-current')->once();

        $repo = Mockery::mock(WeatherRepositoryInterface::class);
        $repo->shouldReceive('getCurrentWeather')
            ->with($city)
            ->once()
            ->andReturn($weatherData);

        $cacheRepo = new WeatherRepositoryCache($repo, $monitor, $cache);
        $result = $cacheRepo->getCurrentWeather($city);

        $this->assertSame($weatherData, $result);
    }

    /**
     * @throws InvalidArgumentException
     * @throws ApiAccessException
     */
    public function test_city_exists_returns_cached_value(): void
    {
        $city = Mockery::mock(City::class);
        $city->shouldReceive('getName')->andReturn('Dnipro');

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('get')
            ->with('weather:Dnipro:city-exists')
            ->once()
            ->andReturn(true);

        $monitor = Mockery::mock(WeatherCacheMonitorInterface::class);
        $monitor->shouldReceive('incrementHit')->with('Dnipro', 'city-exists')->once();

        $repo = Mockery::mock(WeatherRepositoryInterface::class);

        $cacheRepo = new WeatherRepositoryCache($repo, $monitor, $cache);
        $this->assertTrue($cacheRepo->cityExists($city));
    }

    /**
     * @throws InvalidArgumentException
     * @throws ApiAccessException
     */
    public function test_city_exists_calls_repo_and_caches_if_not_found(): void
    {
        $city = Mockery::mock(City::class);
        $city->shouldReceive('getName')->andReturn('Odesa');

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('get')
            ->with('weather:Odesa:city-exists')
            ->once()
            ->andReturn(null);

        $cache->shouldReceive('put')
            ->withArgs(function ($key, $value, $ttl) {
                return $key === 'weather:Odesa:city-exists' && $value === true && $ttl instanceof Carbon;
            })
            ->once();

        $monitor = Mockery::mock(WeatherCacheMonitorInterface::class);
        $monitor->shouldReceive('incrementMiss')->with('Odesa', 'city-exists')->once();

        $repo = Mockery::mock(WeatherRepositoryInterface::class);
        $repo->shouldReceive('cityExists')->with($city)->once()->andReturn(true);

        $cacheRepo = new WeatherRepositoryCache($repo, $monitor, $cache);
        $this->assertTrue($cacheRepo->cityExists($city));
    }
}
