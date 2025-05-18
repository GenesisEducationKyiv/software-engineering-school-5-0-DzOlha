<?php

namespace App\Application\Weather\DTOs;

class WeatherResponseDTO
{
    public function __construct(
        public readonly float $temperature,
        public readonly float $humidity,
        public readonly string $description
    ) {
    }

    public function toArray(): array
    {
        return [
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'description' => $this->description,
        ];
    }
}
