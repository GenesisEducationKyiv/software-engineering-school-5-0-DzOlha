<?php

namespace App\Modules\Weather\Infrastructure\Repositories\Chain;

use App\Modules\Weather\Domain\Repositories\Chain\Handler\WeatherChainHandlerInterface;
use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;
use App\Modules\Weather\Domain\ValueObjects\City\City;
use App\Modules\Weather\Domain\ValueObjects\WeatherData;

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
