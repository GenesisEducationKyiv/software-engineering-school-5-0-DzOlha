<?php

namespace App\Modules\Weather\Presentation\Interface;

use App\Modules\Weather\Domain\ValueObjects\WeatherData;

interface WeatherModuleInterface
{
    public function cityExists(string $city): bool;
    public function getCurrentWeather(string $city): WeatherData;
}
