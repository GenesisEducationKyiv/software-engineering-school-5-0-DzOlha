<?php

namespace App\Domain\Subscription\Factories;
namespace App\Domain\Subscription\ValueObjects\Token\Factory;

use App\Domain\Subscription\ValueObjects\Token\Generator\TokenGeneratorInterface;
use App\Domain\Subscription\ValueObjects\Token\Token;
use App\Domain\Subscription\ValueObjects\Token\TokenType;
use App\Exceptions\ValidationException;
use DateTimeImmutable;
use DateTimeInterface;
use Random\RandomException;

class TokenFactory implements TokenFactoryInterface
{
    public function __construct(
        private readonly TokenGeneratorInterface $generator
    ) {
    }

    /**
     * @throws ValidationException|RandomException
     */
    public function create(TokenType $type, ?DateTimeInterface $customExpiresAt = null): Token
    {
        $expirationTime = $customExpiresAt ?? $this->resolveDefaultExpiration($type);

        return new Token(
            value: $this->generator->generate(),
            type: $type,
            expiresAt: $expirationTime
        );
    }

    /**
     * @throws ValidationException|RandomException
     */
    public function createConfirmation(): Token
    {
        return $this->create(TokenType::CONFIRM);
    }

    /**
     * @throws ValidationException|RandomException
     */
    public function createCancel(): Token
    {
        return $this->create(TokenType::CANCEL);
    }

    /**
     * @throws \Exception
     */
    private function resolveDefaultExpiration(TokenType $type): ?DateTimeInterface
    {
        $expiration = $type->getDefaultExpirationTime();
        return $expiration ? new DateTimeImmutable($expiration) : null;
    }
}
