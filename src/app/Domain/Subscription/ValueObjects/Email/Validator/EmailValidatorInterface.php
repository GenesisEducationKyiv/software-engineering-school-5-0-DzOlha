<?php

namespace App\Domain\Subscription\ValueObjects\Email\Validator;

use App\Exceptions\ValidationException;

interface EmailValidatorInterface
{
    /**
     * @param string $value
     * @return void
     * @throws ValidationException
     */
    public function assertValid(string $value): void;
    public function isValid(string $value): bool;
}
