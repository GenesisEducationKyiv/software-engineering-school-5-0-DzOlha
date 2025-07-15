<?php

namespace App\Modules\Weather\Application\Services;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Modules\Weather\Application\DTOs\WeatherRequestDTO;
use App\Modules\Weather\Domain\ValueObjects\WeatherData;

interface WeatherServiceInterface
{
    /**
     * @param WeatherRequestDTO $dto
     * @return WeatherData
     * @throws CityNotFoundException|ApiAccessException
     */
    public function getCurrentWeather(WeatherRequestDTO $dto): WeatherData;
}
