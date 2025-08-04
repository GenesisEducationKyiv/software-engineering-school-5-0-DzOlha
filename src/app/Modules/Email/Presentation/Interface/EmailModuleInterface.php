<?php

namespace App\Modules\Email\Presentation\Interface;

use App\Modules\Email\Domain\Entities\EmailSubscriptionEntity;
use App\Modules\Email\Domain\Entities\EmailWeatherEntity;

interface EmailModuleInterface
{
    public function sendWeatherUpdate(
        EmailSubscriptionEntity $subscription,
        EmailWeatherEntity $weatherData
    ): bool;

    public function sendConfirmationEmail(EmailSubscriptionEntity $subscription): bool;

    public function getEmailSubscriptionEntity(
        ?int $id,
        string $email,
        string $city,
        string $frequency,
        ?string $confirmationToken,
        ?string $unsubscribeToken
    ): EmailSubscriptionEntity;

    public function getEmailWeatherEntity(
        float $temperature,
        float $humidity,
        string $description
    ): EmailWeatherEntity;
}
