<?php

namespace App\Modules\Email\Domain\Entities;

class EmailWeatherEntity
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
}
