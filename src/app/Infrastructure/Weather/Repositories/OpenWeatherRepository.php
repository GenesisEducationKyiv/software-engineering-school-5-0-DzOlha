<?php

namespace App\Infrastructure\Weather\Repositories;

use App\Domain\Weather\ValueObjects\City\City;
use App\Domain\Weather\ValueObjects\WeatherData;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;

class OpenWeatherRepository extends AbstractWeatherRepository
{
    protected int $cityNotFoundCode = 404;

    public function getProviderName(): string
    {
        return "OpenWeather";
    }

    public function getCurrentWeather(City $city): WeatherData
    {
        $response = $this->httpClient->get($this->apiUrl . $this->currentWeatherEndpoint, [
            'q'     => $city->getName(),
            'appid' => $this->apiKey,
            'units' => 'metric', // return temp in Celsius
        ]);

        if ($response->successful()) {
            /**
             * @var array{main: array{temp: float, humidity: float}, weather: array<array{description: string}>} $data
             */
            $data = $response->json();

            return new WeatherData(
                temperature: $data['main']['temp'],
                humidity: $data['main']['humidity'],
                description: $data['weather'][0]['description'],
            );
        } else {
            if ($this->hasNotFoundError([$response->status()])) {
                throw new CityNotFoundException();
            }
            throw new ApiAccessException();
        }
    }

    public function cityExists(City $city): bool
    {
        $response = $this->httpClient->get($this->apiUrl . $this->currentWeatherEndpoint, [
            'q'     => $city->getName(),
            'appid' => $this->apiKey,
        ]);

        if ($response->successful()) {
            return true;
        }

        if ($this->hasNotFoundError([$response->status()])) {
            return false;
        }

        throw new ApiAccessException();
    }

    /**
     * @param array<string|int, scalar|null> $error
     * @return bool
     */
    protected function hasNotFoundError(array $error): bool
    {
        return $error[0] === $this->cityNotFoundCode;
    }
}
