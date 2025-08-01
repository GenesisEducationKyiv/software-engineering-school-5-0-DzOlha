<?php

namespace App\Infrastructure\Weather\Repositories;

use App\Domain\Weather\ValueObjects\City\City;
use App\Domain\Weather\ValueObjects\WeatherData;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;

class WeatherStackRepository extends AbstractWeatherRepository
{
    protected int $cityNotFoundCode = 615;

    public function getProviderName(): string
    {
        return "WeatherStack";
    }

    public function getCurrentWeather(City $city): WeatherData
    {
        $response = $this->httpClient->get($this->apiUrl . $this->currentWeatherEndpoint, [
            'access_key' => $this->apiKey,
            'query'      => $city->getName(),
        ]);

        if ($response->successful()) {
            /**
             * @var array{
             *     current: array{
             *          temperature: float,
             *          humidity: float,
             *          weather_descriptions: array<string>
             *        },
             *     error?: array{code: int}
             *     } $data
             */
            $data = $response->json();

            if (isset($data['error'])) {
                if ($this->hasNotFoundError($data['error'])) {
                    throw new CityNotFoundException();
                }
                throw new ApiAccessException();
            }

            return new WeatherData(
                temperature: $data['current']['temperature'],
                humidity: $data['current']['humidity'],
                description: $data['current']['weather_descriptions'][0] ?? 'No description',
            );
        }

        throw new ApiAccessException();
    }

    public function cityExists(City $city): bool
    {
        $response = $this->httpClient->get($this->apiUrl . $this->currentWeatherEndpoint, [
            'access_key' => $this->apiKey,
            'query'      => $city->getName(),
        ]);

        if ($response->successful()) {
            /**
             * @var array{error?: array{code: int}} $data
             */
            $data = $response->json();

            if (isset($data['error'])) {
                if ($this->hasNotFoundError($data['error'])) {
                    return false;
                }
                throw new ApiAccessException();
            }

            return true;
        }

        throw new ApiAccessException();
    }
}
