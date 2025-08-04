<?php

namespace App\Modules\Subscription\Domain\ValueObjects\Token\Validator;

use App\Exceptions\ValidationException;

interface TokenValidatorInterface
{
    /**
     * @param string $value
     * @return void
     * @throws ValidationException
     */
    public function assertValid(string $value): void;
    public function isValid(string $value): bool;
}
