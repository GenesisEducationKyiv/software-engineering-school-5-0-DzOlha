<?php

namespace App\Modules\Notification\Domain\Entities;

use App\Modules\Subscription\Domain\Entities\Subscription;

class NotificationSubscriptionEntity
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $email,
        private readonly string $city,
        private readonly string $frequency,
        private readonly ?string $confirmationToken,
        private readonly ?string $unsubscribeToken,
        private readonly bool $isActive,
        private readonly int $intervalMinutes
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function getUnsubscribeToken(): ?string
    {
        return $this->unsubscribeToken;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getFrequency(): string
    {
        return $this->frequency;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getIntervalMinutes(): int
    {
        return $this->intervalMinutes;
    }
}
