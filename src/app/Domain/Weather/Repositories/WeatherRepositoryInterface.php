<?php

namespace App\Domain\Weather\Repositories;

use App\Domain\Weather\ValueObjects\City;
use App\Domain\Weather\ValueObjects\WeatherData;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;

interface WeatherRepositoryInterface
{
    /**
     * @param City $city
     * @return WeatherData
     * @throws CityNotFoundException|ApiAccessException
     */
    public function getCurrentWeather(City $city): WeatherData;
    public function cityExists(City $city): bool;
}
