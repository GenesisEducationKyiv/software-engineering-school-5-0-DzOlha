<?php

namespace App\Modules\Subscription\Domain\ValueObjects\Token;

use App\Exceptions\ValidationException;

enum TokenType: string
{
    case CONFIRM = 'confirm';
    case CANCEL = 'cancel';

    /**
     * Get all available token type values
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
    public static function fromString(string $type): self
    {
        $type = trim($type);

        $tokenType = self::tryFrom($type);

        if ($tokenType === null) {
            throw new ValidationException([
                'token' => ["Invalid token type: {$type}"]
            ]);
        }

        return $tokenType;
    }

    /**
     * Get the default expiration time for this token type
     */
    public function getDefaultExpirationTime(): ?string
    {
        return match ($this) {
            self::CONFIRM => '+24 hours',
            self::CANCEL => null, // Cancel tokens don't expire by default
        };
    }

    /**
     * Check if this token type should have expiration by default
     */
    public function hasDefaultExpiration(): bool
    {
        return match ($this) {
            self::CONFIRM => true,
            self::CANCEL => false,
        };
    }

    /**
     * Get the token length for this type (if different per type in future)
     */
    public function getTokenLength(): int
    {
        return match ($this) {
            self::CONFIRM => 64,
            self::CANCEL => 64,
        };
    }

    /**
     * Check if this is a confirmation token type
     */
    public function isConfirmation(): bool
    {
        return $this === self::CONFIRM;
    }

    /**
     * Check if this is a cancel token type
     */
    public function isCancel(): bool
    {
        return $this === self::CANCEL;
    }
}
