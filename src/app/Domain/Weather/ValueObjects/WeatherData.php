<?php

namespace App\Domain\Weather\ValueObjects;

class WeatherData
{
    public function __construct(
        private readonly float $temperature,
        private readonly float $humidity,
        private readonly string $description
    ) {
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function getHumidity(): float
    {
        return $this->humidity;
    }

    public function getDescription(): string
    {
        return $this->description;
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
