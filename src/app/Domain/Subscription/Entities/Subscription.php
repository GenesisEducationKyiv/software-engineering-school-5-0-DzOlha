<?php

namespace App\Domain\Subscription\Entities;

use App\Domain\Subscription\ValueObjects\Email\Email;
use App\Domain\Subscription\ValueObjects\Frequency\Frequency;
use App\Domain\Subscription\ValueObjects\Status\Status;
use App\Domain\Subscription\ValueObjects\Token\Token;
use App\Domain\Weather\ValueObjects\City\City;

class Subscription
{
    private ?int $id = null;
    private ?Token $confirmationToken = null;
    private ?Token $unsubscribeToken = null;
    private Status $status;

    public function __construct(
        private readonly Email $email,
        private readonly City $city,
        private readonly Frequency $frequency,
        ?Status $status = null
    ) {
        $this->status = $status ?? Status::pending();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getFrequency(): Frequency
    {
        return $this->frequency;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getConfirmationToken(): ?Token
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(Token $token): self
    {
        $this->confirmationToken = $token;
        return $this;
    }

    public function getUnsubscribeToken(): ?Token
    {
        return $this->unsubscribeToken;
    }

    public function setUnsubscribeToken(Token $token): self
    {
        $this->unsubscribeToken = $token;
        return $this;
    }

    public function confirm(): self
    {
        $this->status = Status::active();
        return $this;
    }

    public function cancel(): self
    {
        $this->status = Status::canceled();
        return $this;
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isPending(): bool
    {
        return $this->status->isPending();
    }

    public function isCanceled(): bool
    {
        return $this->status->isCanceled();
    }

    /**
     * @return array{
     *     id: int|null,
     *     email: string,
     *     city: array{name: string},
     *     frequency: array{id: int|null, name: string, interval_minutes: int},
     *     status: string,
     *     confirmation_token: string|null,
     *     unsubscribe_token: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email->getValue(),
            'city' => [
                'name' => $this->city->getName()
            ],
            'frequency' => [
                'id' => $this->frequency->getId(),
                'name' => $this->frequency->getName(),
                'interval_minutes' => $this->frequency->getIntervalMinutes(),
            ],
            'status' => $this->status->getValue(),
            'confirmation_token' => $this->confirmationToken?->__toString(),
            'unsubscribe_token' => $this->unsubscribeToken?->__toString(),
        ];
    }
}
