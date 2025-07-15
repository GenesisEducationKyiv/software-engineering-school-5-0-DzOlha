<?php

namespace App\Modules\Weather\Domain\ValueObjects;

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

    /**
     * @return array{temperature: float, humidity: float, description: string}
     */
    public function toArray(): array
    {
        return [
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'description' => $this->description,
        ];
    }
}
