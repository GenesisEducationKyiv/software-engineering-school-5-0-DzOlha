<?php

namespace App\Modules\Email\Presentation\Interface;

use App\Modules\Email\Application\EmailServiceInterface;
use App\Modules\Email\Domain\Entities\EmailSubscriptionEntity;
use App\Modules\Email\Domain\Entities\EmailWeatherEntity;

readonly class EmailModule implements EmailModuleInterface
{
    public function __construct(
        private EmailServiceInterface $emailService
    ) {
    }

    public function sendWeatherUpdate(
        EmailSubscriptionEntity $subscription,
        EmailWeatherEntity $weatherData
    ): bool {
        return $this->emailService->sendWeatherUpdate($subscription, $weatherData);
    }

    public function sendConfirmationEmail(EmailSubscriptionEntity $subscription): bool
    {
        return $this->emailService->sendConfirmationEmail($subscription);
    }

    public function getEmailSubscriptionEntity(
        ?int $id,
        string $email,
        string $city,
        string $frequency,
        ?string $confirmationToken,
        ?string $unsubscribeToken
    ): EmailSubscriptionEntity {
        return new EmailSubscriptionEntity(
            $id,
            $email,
            $city,
            $frequency,
            $confirmationToken,
            $unsubscribeToken
        );
    }

    public function getEmailWeatherEntity(
        float $temperature,
        float $humidity,
        string $description
    ): EmailWeatherEntity {
        return new EmailWeatherEntity(
            $temperature,
            $humidity,
            $description
        );
    }
}
