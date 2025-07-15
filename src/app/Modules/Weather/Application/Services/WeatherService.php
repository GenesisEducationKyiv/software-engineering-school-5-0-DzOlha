<?php

namespace App\Modules\Weather\Application\Services;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Modules\Weather\Application\DTOs\WeatherRequestDTO;
use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;
use App\Modules\Weather\Domain\ValueObjects\WeatherData;

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
