<?php

namespace Tests\Feature\Modules\Weather\Infrastructure\Repositories\Chain;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\CustomException;
use App\Exceptions\ValidationException;
use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;
use App\Modules\Weather\Domain\ValueObjects\City\City;
use App\Modules\Weather\Domain\ValueObjects\WeatherData;
use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WeatherChainRealApiTest extends TestCase
{
    /**
     * @throws CustomException
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    #[DataProvider('cityExistsProvider')]
    public function test_city_exists_real_api(string $cityName, bool $expectedResult): void
    {
        /** @var WeatherRepositoryInterface $chain */
        $chain = $this->app->make(WeatherRepositoryInterface::class);

        $result = $chain->cityExists(new City($cityName));

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @throws CityNotFoundException|ApiAccessException
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    #[DataProvider('getCurrentWeatherProvider')]
    public function test_get_current_weather_real_api(string $cityName, bool $expectException = false): void
    {
        if ($expectException) {
            $this->expectException(CustomException::class);
        }

        /** @var WeatherRepositoryInterface $chain */
        $chain = $this->app->make(WeatherRepositoryInterface::class);

        $result = $chain->getCurrentWeather(new City($cityName));

        if (!$expectException) {
            $this->assertInstanceOf(WeatherData::class, $result);
        }
    }

    public static function cityExistsProvider(): array
    {
        return [
            'city_exists_Kyiv' => ['Kyiv', true],
            'city_exists_London' => ['London', true],

            'city_does_not_exist_InvalidCityForWeatherApi' => ['InvalidCityNameForWeatherApiXYZ', false],
            'city_does_not_exist_InvalidCityForOpenWeather' => ['InvalidCityNameForOpenWeatherXYZ', false],
            'city_does_not_exist_InvalidCityForWeatherStack' => ['InvalidCityNameForWeatherStackXYZ', false],
        ];
    }

    public static function getCurrentWeatherProvider(): array
    {
        return [
            'get_weather_success_Kyiv' => ['Kyiv', false],
            'get_weather_success_London' => ['London', false],

            'get_weather_failure_InvalidCityForWeatherApi' => ['InvalidCityNameForWeatherApiXYZ', true],
            'get_weather_failure_InvalidCityForOpenWeather' => ['InvalidCityNameForOpenWeatherXYZ', true],
            'get_weather_failure_InvalidCityForWeatherStack' => ['InvalidCityNameForWeatherStackXYZ', true],
        ];
    }
}
