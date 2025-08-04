<?php

namespace App\Modules\Notification\Domain\Entities;

class NotificationSubscriptionEntity
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $email,
        private readonly string $city,
        private readonly string $frequency,
        private readonly ?string $confirmationToken,
        private readonly ?string $unsubscribeToken,
        private readonly ?bool $isActive,
        private readonly ?int $intervalMinutes = null
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

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function getIntervalMinutes(): ?int
    {
        return $this->intervalMinutes;
    }

    /**
     * @param array{
     *     id: int|null,
     *     email: string,
     *     city: array{name: string},
     *     frequency: array{id: int|null, name: string, interval_minutes: int},
     *     status: string,
     *     confirmation_token: string|null,
     *     unsubscribe_token: string|null
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            email: $data['email'],
            city: $data['city']['name'],
            frequency: $data['frequency']['name'],
            confirmationToken: $data['confirmation_token'] ?? null,
            unsubscribeToken: $data['unsubscribe_token'] ?? null,
            isActive: $data['status'] === 'active'
        );
    }
}
