<?php

namespace App\Application\Subscription\Emails\Mails\Confirmation;

use App\Domain\Subscription\Entities\Subscription;

readonly class ConfirmationMailData
{
    private string $confirmUrl;
    private string $frequency;
    private string $city;
    private string $email;

    public function __construct(
        string $confirmUrl,
        Subscription $subscription
    ) {
        $this->confirmUrl = $confirmUrl;
        $this->frequency = $subscription->getFrequency()->getName();
        $this->city = $subscription->getCity()->getName();
        $this->email = $subscription->getEmail()->getValue();
    }

    public function getConfirmUrl(): string
    {
        return $this->confirmUrl;
    }

    public function getFrequency(): string
    {
        return $this->frequency;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
