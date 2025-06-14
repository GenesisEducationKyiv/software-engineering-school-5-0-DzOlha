<?php

namespace App\Application\Weather\Services;

use App\Application\Weather\DTOs\WeatherRequestDTO;
use App\Domain\Weather\ValueObjects\WeatherData;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;

interface WeatherServiceInterface
{
    /**
     * @param WeatherRequestDTO $dto
     * @return WeatherData
     * @throws CityNotFoundException|ApiAccessException
     */
    public function getCurrentWeather(WeatherRequestDTO $dto): WeatherData;
}
