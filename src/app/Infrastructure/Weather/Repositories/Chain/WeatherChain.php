<?php

namespace App\Infrastructure\Weather\Repositories\Chain;

use App\Domain\Weather\Repositories\Chain\Handler\WeatherChainHandlerInterface;
use App\Domain\Weather\Repositories\WeatherRepositoryInterface;
use App\Domain\Weather\ValueObjects\City\City;
use App\Domain\Weather\ValueObjects\WeatherData;

class WeatherChain implements WeatherRepositoryInterface
{
    public function __construct(
        private readonly WeatherChainHandlerInterface $handler
    ) {
    }

    public function getCurrentWeather(City $city): WeatherData
    {
        return $this->handler->getCurrentWeather($city);
    }

    public function cityExists(City $city): bool
    {
        return $this->handler->cityExists($city);
    }
}
