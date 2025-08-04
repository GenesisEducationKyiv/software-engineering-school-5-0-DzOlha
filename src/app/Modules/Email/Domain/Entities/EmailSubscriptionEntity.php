<?php

namespace App\Modules\Email\Domain\Entities;

class EmailSubscriptionEntity
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $email,
        private readonly string $city,
        private readonly string $frequency,
        private readonly ?string $confirmationToken,
        private readonly ?string $unsubscribeToken
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

    public function getCity(): string
    {
        return $this->city;
    }

    public function getFrequency(): string
    {
        return $this->frequency;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function getUnsubscribeToken(): ?string
    {
        return $this->unsubscribeToken;
    }
}
