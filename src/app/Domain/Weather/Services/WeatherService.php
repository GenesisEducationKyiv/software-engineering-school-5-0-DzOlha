<?php

namespace App\Domain\Weather\Services;

use App\Application\Weather\DTOs\WeatherRequestDTO;
use App\Domain\Weather\Repositories\WeatherRepositoryInterface;
use App\Domain\Weather\ValueObjects\City;
use App\Domain\Weather\ValueObjects\WeatherData;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;

class WeatherService
{
    public function __construct(
        private readonly WeatherRepositoryInterface $weatherRepository
    ) {
    }

    /**
     * @param City $city
     * @return WeatherData
     * @throws CityNotFoundException|ApiAccessException
     */
    public function getCurrentWeather(WeatherRequestDTO $dto): WeatherData
    {
        return $this->weatherRepository->getCurrentWeather($dto->city);
    }
}
