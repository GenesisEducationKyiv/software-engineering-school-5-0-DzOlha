<?php

namespace Tests\Unit\Infrastructure\Weather\Repositories\Chain\Handler;

use App\Domain\Weather\Repositories\WeatherRepositoryInterface;
use App\Domain\Weather\ValueObjects\City\City;
use App\Domain\Weather\ValueObjects\WeatherData;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Infrastructure\Weather\Repositories\Chain\Handler\WeatherChainHandler;
use Mockery;
use Tests\TestCase;

class WeatherChainHandlerTest extends TestCase
{
    /**
     * @throws ApiAccessException
     * @throws CityNotFoundException
     */
    public function test_returns_weather_data_from_current()
    {
        $city = Mockery::mock(City::class);
        $weather_data = Mockery::mock(WeatherData::class);

        $repo = Mockery::mock(WeatherRepositoryInterface::class);
        $repo->shouldReceive('getCurrentWeather')
            ->once()
            ->with($city)
            ->andReturn($weather_data);

        $handler = new WeatherChainHandler($repo);

        $this->assertSame($weather_data, $handler->getCurrentWeather($city));
    }

    /**
     * @throws CityNotFoundException
     * @throws ApiAccessException
     */
    public function test_falls_back_to_next_handler_on_exception()
    {
        $city = Mockery::mock(City::class);
        $weather_data = Mockery::mock(WeatherData::class);

        $repo1 = Mockery::mock(WeatherRepositoryInterface::class);
        $repo1->shouldReceive('getCurrentWeather')
            ->once()
            ->with($city)
            ->andThrow(new \Exception('Primary failed'));

        $repo2 = Mockery::mock(WeatherRepositoryInterface::class);
        $repo2->shouldReceive('getCurrentWeather')
            ->once()
            ->with($city)
            ->andReturn($weather_data);

        $handler2 = new WeatherChainHandler($repo2);
        $handler1 = new WeatherChainHandler($repo1, $handler2);

        $this->assertSame($weather_data, $handler1->getCurrentWeather($city));
    }

    public function test_throws_exception_if_all_fail()
    {
        $this->expectException(\Exception::class);

        $city = Mockery::mock(City::class);

        $repo1 = Mockery::mock(WeatherRepositoryInterface::class);
        $repo1->shouldReceive('getCurrentWeather')
            ->once()
            ->with($city)
            ->andThrow(new \Exception('Fail 1'));

        $repo2 = Mockery::mock(WeatherRepositoryInterface::class);
        $repo2->shouldReceive('getCurrentWeather')
            ->once()
            ->with($city)
            ->andThrow(new \Exception('Fail 2'));

        $handler2 = new WeatherChainHandler($repo2);
        $handler1 = new WeatherChainHandler($repo1, $handler2);

        $handler1->getCurrentWeather($city);
    }

    /**
     * @throws ApiAccessException
     */
    public function test_city_exists_checks_all_if_needed()
    {
        $city = Mockery::mock(City::class);

        $repo1 = Mockery::mock(WeatherRepositoryInterface::class);
        $repo1->shouldReceive('cityExists')
            ->once()
            ->with($city)
            ->andReturn(false);

        $repo2 = Mockery::mock(WeatherRepositoryInterface::class);
        $repo2->shouldReceive('cityExists')
            ->once()
            ->with($city)
            ->andReturn(true);

        $handler2 = new WeatherChainHandler($repo2);
        $handler1 = new WeatherChainHandler($repo1, $handler2);

        $this->assertTrue($handler1->cityExists($city));
    }

    /**
     * @throws ApiAccessException
     */
    public function test_city_exists_throws_if_all_fail()
    {
        $this->expectException(\Exception::class);

        $city = Mockery::mock(City::class);

        $repo1 = Mockery::mock(WeatherRepositoryInterface::class);
        $repo1->shouldReceive('cityExists')
            ->once()
            ->with($city)
            ->andThrow(new \Exception());

        $handler = new WeatherChainHandler($repo1);
        $handler->cityExists($city);
    }
}
