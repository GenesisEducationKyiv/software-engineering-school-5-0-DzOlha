<?php

namespace App\Application\Weather\DTOs;

use App\Domain\Weather\ValueObjects\City\City;

class WeatherRequestDTO
{
    public function __construct(
        public readonly City $city
    ) {
    }
}
