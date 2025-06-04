<?php

namespace App\Domain\Subscription\ValueObjects;

use App\Exceptions\ValidationException;
use DateTimeInterface;
use Random\RandomException;

class Token
{
    private const CONFIRM_TYPE = 'confirm';
    private const CANCEL_TYPE = 'cancel';

    private const TOKEN_LENGTH = 64;

    private const EXPIRATION_TIME_CONFIRM = '+24 hours';

    /**
     * @throws ValidationException
     */
    public function __construct(
        private readonly string $value,
        private readonly string $type,
        private readonly ?DateTimeInterface $expiresAt = null
    ) {
        $this->validateType($type);
        $this->validateTokenFormat($value);
    }

    /**
     * @throws ValidationException
     */
    private function validateType($type): void
    {
        $type = trim($type);

        if (!in_array($type, [self::CONFIRM_TYPE, self::CANCEL_TYPE])) {
            throw new ValidationException([
                'token' => ["Invalid token type: {$type}"]
            ]);
        }
    }

    /**
     * Validates that the token is a 64-character hexadecimal string.
     *
     * @param string $token The token to validate
     * @throws \InvalidArgumentException If the token is not a 64-character hexadecimal string
     * @throws ValidationException
     */
    private function validateTokenFormat(string $token): void
    {
        $token = trim($token);

        if (strlen($token) !== self::TOKEN_LENGTH) {
            throw new ValidationException([
                'token' => ["Token must be exactly " . self::TOKEN_LENGTH . " characters long."]
            ]);
        }

        if (!ctype_xdigit($token)) {
            throw new ValidationException([
                'token' => ["Token must contain only hexadecimal characters (0-9, a-f)."]
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    public static function confirmation(string $token): self
    {
        return new self($token, self::CONFIRM_TYPE);
    }

    /**
     * @throws ValidationException
     */
    public static function cancel(string $token): self
    {
        return new self($token, self::CANCEL_TYPE);
    }

    /**
     * @throws ValidationException
     * @throws RandomException
     */
    public static function createConfirmation(): self
    {
        return new self(
            bin2hex(random_bytes(self::TOKEN_LENGTH / 2)),
            self::CONFIRM_TYPE,
            new \DateTimeImmutable(self::EXPIRATION_TIME_CONFIRM)
        );
    }

    /**
     * @throws ValidationException
     * @throws RandomException
     */
    public static function createUnsubscribe(): self
    {
        return new self(
            bin2hex(random_bytes(self::TOKEN_LENGTH / 2)),
            self::CANCEL_TYPE
        );
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function isConfirmationType(): bool
    {
        return $this->type === self::CONFIRM_TYPE;
    }

    public function isCancelType(): bool
    {
        return $this->type === self::CANCEL_TYPE;
    }

    public function equals(Token $other): bool
    {
        return $this->value === $other->getValue() && $this->type === $other->getType();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
