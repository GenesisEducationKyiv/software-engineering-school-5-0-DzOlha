<?php

namespace App\Domain\Subscription\ValueObjects\Token\Factory;

use App\Domain\Subscription\ValueObjects\Token\Token;
use App\Domain\Subscription\ValueObjects\Token\TokenType;
use DateTimeInterface;

interface TokenFactoryInterface
{
    public function create(TokenType $type, ?DateTimeInterface $customExpiresAt = null): Token;
    public function createConfirmation(): Token;
    public function createCancel(): Token;
}
