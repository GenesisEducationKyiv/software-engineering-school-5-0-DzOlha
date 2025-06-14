<?php

namespace App\Domain\Subscription\ValueObjects\Token;

use App\Domain\Subscription\ValueObjects\Token\Generator\TokenGenerator;
use App\Domain\Subscription\ValueObjects\Token\Generator\TokenGeneratorInterface;
use App\Domain\Subscription\ValueObjects\Token\Validator\TokenValidator;
use App\Domain\Subscription\ValueObjects\Token\Validator\TokenValidatorInterface;
use App\Exceptions\ValidationException;
use DateTimeInterface;
use Random\RandomException;

class Token
{
    /**
     * @throws ValidationException
     */
    public function __construct(
        private readonly string $value,
        private readonly TokenType $type,
        private readonly ?DateTimeInterface $expiresAt = null,
        private ?TokenValidatorInterface $validator = null,
    ) {
        if (!$this->validator) {
            $this->validator = new TokenValidator();
        }

        $this->validator->assertValid($this->value);
    }

    /**
     * @throws ValidationException
     */
    public static function confirmation(string $token): self
    {
        return new self($token, TokenType::CONFIRM);
    }

    /**
     * @throws ValidationException
     */
    public static function cancel(string $token): self
    {
        return new self($token, TokenType::CANCEL);
    }

    /**
     * @throws ValidationException
     * @throws RandomException
     * @throws \Exception
     */
    public static function createConfirmation(
        TokenGeneratorInterface $generator = new TokenGenerator()
    ): self {
        $type = TokenType::CONFIRM;
        $expirationTime = $type->getDefaultExpirationTime();
        $expiresAt = $expirationTime !== null
            ? new \DateTimeImmutable($expirationTime)
            : null;

        return new self($generator->generate(), $type, $expiresAt);
    }

    /**
     * @throws ValidationException
     * @throws RandomException
     * @throws \Exception
     */
    public static function createUnsubscribe(
        TokenGeneratorInterface $generator = new TokenGenerator()
    ): self {
        $type = TokenType::CANCEL;
        $expirationTime = $type->getDefaultExpirationTime();
        $expiresAt = $expirationTime !== null
            ? new \DateTimeImmutable($expirationTime)
            : null;

        return new self($generator->generate(), $type, $expiresAt);
    }

    /**
     * Create from TokenType enum directly
     *
     * @throws ValidationException
     */
    public static function fromType(string $token, TokenType $type, ?DateTimeInterface $expiresAt = null): self
    {
        return new self($token, $type, $expiresAt);
    }

    /**
     * Create from string type
     *
     * @throws ValidationException
     */
    public static function fromStringType(string $token, string $type, ?DateTimeInterface $expiresAt = null): self
    {
        $tokenType = TokenType::fromString($type);
        return new self($token, $tokenType, $expiresAt);
    }

    /**
     * Generate a new token of the specified type
     *
     * @throws ValidationException
     * @throws RandomException
     * @throws \Exception
     */
    public static function generate(
        TokenType $type,
        ?DateTimeInterface $customExpiresAt = null,
        TokenGeneratorInterface $generator = new TokenGenerator()
    ): self {
        $expirationTime = $type->getDefaultExpirationTime();

        $expirationTime = $expirationTime !== null
            ? new \DateTimeImmutable($expirationTime)
            : null;

        $expiresAt = $customExpiresAt ?? $expirationTime;

        return new self($generator->generate(), $type, $expiresAt);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type->value;
    }

    public function getTokenType(): TokenType
    {
        return $this->type;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false; // Non-expiring tokens are never expired
        }

        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function isConfirmationType(): bool
    {
        return $this->type->isConfirmation();
    }

    public function isCancelType(): bool
    {
        return $this->type->isCancel();
    }

    public function equals(Token $other): bool
    {
        return $this->value === $other->getValue() && $this->type === $other->getTokenType();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Get all available token types
     * @return array<string>
     */
    public static function getAvailableTypes(): array
    {
        return TokenType::values();
    }

    /**
     * Check if token is valid (not expired and properly formatted)
     */
    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    /**
     * Refresh the token with a new expiration time
     *
     * @throws ValidationException
     * @throws RandomException
     */
    public function refresh(): self
    {
        return self::generate($this->type);
    }
}
