<?php

namespace App\Domain\Weather\ValueObjects\City\Validator;

use App\Exceptions\ValidationException;

interface CityValidatorInterface
{
    /**
     * @param string $value
     * @return void
     * @throws ValidationException
     */
    public function assertValid(string $value): void;

    public function isValid(string $value): bool;
}
