<?php

namespace App\Modules\Weather\Domain\Repositories;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Modules\Weather\Domain\ValueObjects\City\City;
use App\Modules\Weather\Domain\ValueObjects\WeatherData;

interface WeatherRepositoryInterface
{
    /**
     * @param City $city
     * @return WeatherData
     * @throws CityNotFoundException|ApiAccessException
     */
    public function getCurrentWeather(City $city): WeatherData;

    /**
     * @throws ApiAccessException
     */
    public function cityExists(City $city): bool;
}
