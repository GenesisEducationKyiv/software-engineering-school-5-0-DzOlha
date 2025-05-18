<?php

namespace App\Infrastructure\Weather\ExternalServices;

use App\Domain\Weather\Repositories\WeatherRepositoryInterface;
use App\Domain\Weather\ValueObjects\City;
use App\Domain\Weather\ValueObjects\WeatherData;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use Illuminate\Support\Facades\Http;

class WeatherApiService implements WeatherRepositoryInterface
{
    private string $apiKey;
    private string $apiUrl;

    private int $cityNotFoundCode = 1006;

    private string $currentWeatherEndpoint = '/current.json';

    public function __construct()
    {
        $this->apiKey = config('services.weather.api_key');
        $this->apiUrl = config('services.weather.api_url');
    }

    /**
     * Get current weather for a city
     *
     * @param string $city
     * @return array
     * @throws CityNotFoundException|ApiAccessException
     */
    public function getCurrentWeather(City $city): WeatherData
    {
        try {
            $response = Http::get($this->apiUrl . $this->currentWeatherEndpoint, [
                'key' => $this->apiKey,
                'q' => $city->getName()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return new WeatherData(
                    temperature: $data['current']['temp_c'],
                    humidity: $data['current']['humidity'],
                    description: $data['current']['condition']['text'],
                );
            } else {
                $data = $response->json();
                if($data['error']['code'] === $this->cityNotFoundCode) {
                    throw new CityNotFoundException();
                }
                throw new ApiAccessException();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function cityExists(City $city): bool
    {
        try {
            $response = Http::get($this->apiUrl . $this->currentWeatherEndpoint, [
                'key' => $this->apiKey,
                'q' => $city->getName()
            ]);

            if ($response->successful()) {
               return true;
            } else {
                $data = $response->json();
                if($data['error']['code'] === $this->cityNotFoundCode) {
                    return false;
                }
                throw new ApiAccessException();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
