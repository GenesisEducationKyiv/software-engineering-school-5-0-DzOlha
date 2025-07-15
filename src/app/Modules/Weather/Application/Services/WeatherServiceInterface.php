<?php

namespace App\Application\Weather\Services;

use App\Domain\Weather\ValueObjects\WeatherData;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Modules\Weather\Application\DTOs\WeatherRequestDTO;

interface WeatherServiceInterface
{
    /**
     * @param WeatherRequestDTO $dto
     * @return WeatherData
     * @throws CityNotFoundException|ApiAccessException
     */
    public function getCurrentWeather(WeatherRequestDTO $dto): WeatherData;
}
