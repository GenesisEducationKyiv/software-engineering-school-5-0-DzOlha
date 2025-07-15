<?php

namespace App\Modules\Subscription\Domain\ValueObjects\Status;

use App\Exceptions\ValidationException;

enum StatusType: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case CANCELED = 'canceled';

    /**
     * Get all available status type values
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Create from string with validation
     *
     * @throws ValidationException
     */
    public static function fromString(string $status): self
    {
        $status = trim($status);

        $type = self::tryFrom($status);

        if ($type === null) {
            throw new ValidationException([
                'status' => ["Invalid status: {$status}"]
            ]);
        }

        return $type;
    }

    /**
     * Check if this is a pending status
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if this is an active status
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Check if this is a canceled status
     */
    public function isCanceled(): bool
    {
        return $this === self::CANCELED;
    }

    /**
     * Check if status allows transitions to active
     */
    public function canTransitionToActive(): bool
    {
        return match ($this) {
            self::PENDING => true,
            self::ACTIVE => false,
            self::CANCELED => false,
        };
    }

    /**
     * Check if status allows cancellation
     */
    public function canBeCanceled(): bool
    {
        return match ($this) {
            self::PENDING => true,
            self::ACTIVE => true,
            self::CANCELED => false,
        };
    }
}
