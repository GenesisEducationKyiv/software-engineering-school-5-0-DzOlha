<?php

namespace App\Modules\Weather\Domain\ValueObjects\City\Validator;

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
