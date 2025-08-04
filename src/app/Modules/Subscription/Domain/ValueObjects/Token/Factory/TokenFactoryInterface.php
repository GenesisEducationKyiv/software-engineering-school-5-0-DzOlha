<?php

namespace App\Modules\Subscription\Domain\ValueObjects\Token\Factory;

use App\Modules\Subscription\Domain\ValueObjects\Token\Token;
use App\Modules\Subscription\Domain\ValueObjects\Token\TokenType;
use DateTimeInterface;

interface TokenFactoryInterface
{
    public function create(TokenType $type, ?DateTimeInterface $customExpiresAt = null): Token;
    public function createConfirmation(): Token;
    public function createCancel(): Token;
}
