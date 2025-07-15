<?php

namespace App\Modules\Email\Application\Mails\Confirmation;

use App\Modules\Email\Domain\Entities\EmailSubscriptionEntity;

readonly class ConfirmationMailData
{
    private string $confirmUrl;
    private string $frequency;
    private string $city;
    private string $email;

    public function __construct(
        string $confirmUrl,
        EmailSubscriptionEntity $subscription
    ) {
        $this->confirmUrl = $confirmUrl;
        $this->frequency = $subscription->getFrequency();
        $this->city = $subscription->getCity();
        $this->email = $subscription->getEmail();
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
