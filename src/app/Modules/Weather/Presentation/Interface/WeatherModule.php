<?php

namespace App\Modules\Weather\Presentation\Interface;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\ValidationException;
use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;
use App\Modules\Weather\Domain\ValueObjects\City\City;
use App\Modules\Weather\Domain\ValueObjects\WeatherData;

readonly class WeatherModule implements WeatherModuleInterface
{
    public function __construct(
        private WeatherRepositoryInterface $repository,
    ) {
    }

    /**
     * @throws ValidationException
     * @throws ApiAccessException
     */
    public function cityExists(string $city): bool
    {
        return $this->repository->cityExists(new City($city));
    }

    /**
     * @throws ValidationException
     * @throws ApiAccessException
     * @throws CityNotFoundException
     */
    public function getCurrentWeather(string $city): WeatherData
    {
        return $this->repository->getCurrentWeather(new City($city));
    }
}
