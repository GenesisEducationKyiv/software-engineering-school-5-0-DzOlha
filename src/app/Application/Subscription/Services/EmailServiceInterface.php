<?php

namespace App\Application\Subscription\Services;

use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Weather\ValueObjects\WeatherData;

interface EmailServiceInterface
{
    public function sendConfirmationEmail(Subscription $subscription): bool;
    public function sendWeatherUpdate(Subscription $subscription, WeatherData $weatherData): bool;
}
