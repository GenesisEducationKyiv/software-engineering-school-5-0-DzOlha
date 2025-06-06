<?php

namespace App\Domain\Subscription\ValueObjects;

use App\Exceptions\ValidationException;

class Status
{
    public const PENDING = 'pending';
    public const ACTIVE = 'active';
    public const CANCELED = 'canceled';

    /**
     * @throws ValidationException
     */
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    /**
     * @throws ValidationException
     */
    private function validate(string $status): void
    {
        $status = trim($status);

        if (!in_array($status, [self::ACTIVE, self::CANCELED, self::PENDING])) {
            throw new ValidationException([
                'status' => ["Invalid status: {$status}"]
            ]);
        }
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function canceled(): self
    {
        return new self(self::CANCELED);
    }

    /**
     * @throws ValidationException
     */
    public static function fromString(string $status): self
    {
        return match ($status) {
            self::PENDING => self::pending(),
            self::ACTIVE => self::active(),
            self::CANCELED => self::canceled(),

            default => throw new ValidationException([
                'status' => ["Invalid status: {$status}"]
            ])
        };
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }

    public function isCanceled(): bool
    {
        return $this->value === self::CANCELED;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Status $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
