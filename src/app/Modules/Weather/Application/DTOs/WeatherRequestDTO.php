<?php

namespace App\Modules\Weather\Application\DTOs;

use App\Modules\Weather\Domain\ValueObjects\City\City;

class WeatherRequestDTO
{
    public function __construct(
        public readonly City $city
    ) {
    }
}
