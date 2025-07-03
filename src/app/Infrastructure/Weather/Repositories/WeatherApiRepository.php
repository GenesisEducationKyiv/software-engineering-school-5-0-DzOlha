<?php

namespace App\Infrastructure\Weather\Repositories;

use App\Domain\Weather\ValueObjects\City\City;
use App\Domain\Weather\ValueObjects\WeatherData;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;

class WeatherApiRepository extends AbstractWeatherRepository
{
    protected int $cityNotFoundCode = 1006;

    public function getProviderName(): string
    {
        return "WeatherApi";
    }

    /**
     * Get current weather for a city
     *
     * @param City $city
     * @return WeatherData
     * @throws CityNotFoundException|ApiAccessException
     */
    public function getCurrentWeather(City $city): WeatherData
    {
        $response = $this->httpClient->get($this->apiUrl . $this->currentWeatherEndpoint, [
            'key' => $this->apiKey,
            'q'   => $city->getName()
        ]);

        if ($response->successful()) {
            /**
             * @var array{current:array{temp_c: float, humidity: float, condition: array{text: string}}} $data
             */
            $data = $response->json();

            return new WeatherData(
                temperature: $data['current']['temp_c'],
                humidity: $data['current']['humidity'],
                description: $data['current']['condition']['text'],
            );
        } else {
            /**
             * @var array{error:array{code: int}} $data
             */
            $data = $response->json();
            if ($this->hasNotFoundError($data['error'])) {
                throw new CityNotFoundException();
            }
            throw new ApiAccessException();
        }
    }

    /**
     * @throws ApiAccessException
     */
    public function cityExists(City $city): bool
    {
        $response = $this->httpClient->get($this->apiUrl . $this->currentWeatherEndpoint, [
            'key' => $this->apiKey,
            'q'   => $city->getName()
        ]);

        if ($response->successful()) {
            return true;
        } else {
            /**
             * @var array{error:array{code: int}} $data
             */
            $data = $response->json();

            if ($this->hasNotFoundError($data['error'])) {
                return false;
            }
            throw new ApiAccessException();
        }
    }
}
