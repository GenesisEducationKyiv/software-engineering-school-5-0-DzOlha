<?php

namespace App\Modules\Subscription\Domain\ValueObjects\Token;

use App\Exceptions\ValidationException;
use App\Modules\Subscription\Domain\ValueObjects\Token\Validator\TokenValidator;
use App\Modules\Subscription\Domain\ValueObjects\Token\Validator\TokenValidatorInterface;
use DateTimeInterface;

class Token
{
    /**
     * @throws ValidationException
     */
    public function __construct(
        private readonly string $value,
        private readonly TokenType $type,
        private readonly ?DateTimeInterface $expiresAt = null,
        ?TokenValidatorInterface $validator = null,
    ) {
        ($validator ?? new TokenValidator())->assertValid($this->value);
    }

    public function getValue(): string
    {
        return $this->value;
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
        return $this->expiresAt !== null && $this->expiresAt < new \DateTimeImmutable();
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    public function isConfirmationType(): bool
    {
        return $this->type->isConfirmation();
    }

    public function isCancelType(): bool
    {
        return $this->type->isCancel();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Token $other): bool
    {
        return $this->value === $other->getValue() && $this->type === $other->getTokenType();
    }

    /**
     * @return array<string>
     */
    public static function getAvailableTypes(): array
    {
        return TokenType::values();
    }
}
