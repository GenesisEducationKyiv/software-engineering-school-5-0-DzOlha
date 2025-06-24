<?php

namespace App\Application\Weather\Queries;

use App\Application\Weather\DTOs\WeatherRequestDTO;
use App\Application\Weather\DTOs\WeatherResponseDTO;
use App\Application\Weather\Services\WeatherServiceInterface;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;

class GetCurrentWeatherQuery
{
    public function __construct(
        private readonly WeatherServiceInterface $weatherService
    ) {
    }

    /**
     * @throws CityNotFoundException|ApiAccessException
     */
    public function execute(WeatherRequestDTO $dto): WeatherResponseDTO
    {
        $weatherData = $this->weatherService->getCurrentWeather($dto);

        return new WeatherResponseDTO(
            $weatherData->getTemperature(),
            $weatherData->getHumidity(),
            $weatherData->getDescription()
        );
    }
}
