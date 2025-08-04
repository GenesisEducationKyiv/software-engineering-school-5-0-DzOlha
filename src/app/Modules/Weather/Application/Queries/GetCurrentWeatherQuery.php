<?php

namespace App\Modules\Weather\Application\Queries;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Modules\Weather\Application\DTOs\WeatherRequestDTO;
use App\Modules\Weather\Application\DTOs\WeatherResponseDTO;
use App\Modules\Weather\Application\Services\WeatherServiceInterface;

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
