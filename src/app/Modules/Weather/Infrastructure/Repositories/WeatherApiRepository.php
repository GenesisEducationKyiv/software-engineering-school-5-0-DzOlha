<?php

namespace App\Modules\Weather\Infrastructure\Repositories;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Modules\Weather\Domain\ValueObjects\City\City;
use App\Modules\Weather\Domain\ValueObjects\WeatherData;

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

            $this->monitor->metrics()->incrementWeatherFetches(
                $this->getProviderName(), $city->getName(), true
            );

            return new WeatherData(
                temperature: $data['current']['temp_c'],
                humidity: $data['current']['humidity'],
                description: $data['current']['condition']['text'],
            );
        } else {
            $this->monitor->metrics()->incrementWeatherFetches(
                $this->getProviderName(), $city->getName(), false
            );
            /**
             * @var array{error:array{code: int}} $data
             */
            $data = $response->json();
            if ($this->hasNotFoundError($data['error'])) {
                $this->logCityNotFound($city);
                throw new CityNotFoundException();
            }
            $this->logApiAccessError($city);
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
                $this->logCityNotFound($city);
                return false;
            }
            $this->monitor->logger()->logError(
                "API access error: {$this->getProviderName()}",
                [
                    'city' => $city->getName(),
                    'module' => $this->getModuleName(),
                    'provider' => $this->getProviderName(),
                ]
            );
            throw new ApiAccessException();
        }
    }
}
