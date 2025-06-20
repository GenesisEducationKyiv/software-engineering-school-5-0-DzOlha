<?php

namespace App\Infrastructure\Weather\Services;

use App\Application\Weather\DTOs\WeatherRequestDTO;
use App\Application\Weather\Services\WeatherServiceInterface;
use App\Domain\Weather\Repositories\WeatherRepositoryInterface;
use App\Domain\Weather\ValueObjects\WeatherData;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;

class WeatherService implements WeatherServiceInterface
{
    public function __construct(
        private readonly WeatherRepositoryInterface $weatherRepository
    ) {
    }

    /**
     * @param WeatherRequestDTO $dto
     * @return WeatherData
     * @throws CityNotFoundException|ApiAccessException
     */
    public function getCurrentWeather(WeatherRequestDTO $dto): WeatherData
    {
        return $this->weatherRepository->getCurrentWeather($dto->city);
    }
}
