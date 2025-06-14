<?php

namespace App\Infrastructure\Subscription\Mails\Update;

use App\Domain\Weather\ValueObjects\WeatherData;

readonly class WeatherUpdateMailData
{
    private string $city;
    private string $frequency;
    private float $temperature;
    private float $humidity;
    private string $description;
    private string $unsubscribeUrl;

    public function __construct(
        string $city,
        string $frequency,
        WeatherData $weatherData,
        string $unsubscribeUrl
    ) {
        $this->city = $city;
        $this->frequency = $frequency;
        $this->temperature = $weatherData->getTemperature();
        $this->humidity = $weatherData->getHumidity();
        $this->description = $weatherData->getDescription();
        $this->unsubscribeUrl = $unsubscribeUrl;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getFrequency(): string
    {
        return $this->frequency;
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

    public function getUnsubscribeUrl(): string
    {
        return $this->unsubscribeUrl;
    }
}
