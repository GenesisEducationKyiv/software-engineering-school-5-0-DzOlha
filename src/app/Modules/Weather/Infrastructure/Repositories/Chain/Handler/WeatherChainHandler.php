<?php

namespace App\Modules\Weather\Infrastructure\Repositories\Chain\Handler;

use App\Modules\Weather\Domain\Repositories\Chain\Handler\WeatherChainHandlerInterface;
use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;
use App\Modules\Weather\Domain\ValueObjects\City\City;
use App\Modules\Weather\Domain\ValueObjects\WeatherData;

class WeatherChainHandler implements WeatherChainHandlerInterface
{
    public function __construct(
        protected WeatherRepositoryInterface $current,
        protected ?WeatherChainHandlerInterface $next = null
    ) {
    }

    public function setNext(?WeatherChainHandlerInterface $handler): void
    {
        $this->next = $handler;
    }

    public function getCurrentWeather(City $city): WeatherData
    {
        try {
            return $this->current->getCurrentWeather($city);
        } catch (\Exception $e) {
            if ($this->next) {
                return $this->next->getCurrentWeather($city);
            } else {
                throw $e;
            }
        }
    }

    public function cityExists(City $city): bool
    {
        try {
            $exists = $this->current->cityExists($city);
            if (!$exists && $this->next) {
                return $this->next->cityExists($city);
            }
            return $exists;
        } catch (\Exception $e) {
            if ($this->next) {
                return $this->next->cityExists($city);
            } else {
                throw $e;
            }
        }
    }
}
