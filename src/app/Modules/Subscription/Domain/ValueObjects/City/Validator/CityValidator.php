<?php

namespace App\Modules\Subscription\Domain\ValueObjects\City\Validator;

use App\Exceptions\ValidationException;

class CityValidator implements CityValidatorInterface
{
    private const MIN_LENGTH = 2;
    private const MAX_LENGTH = 50;

    /**
     * @throws ValidationException
     */
    public function assertValid(string $value): void
    {
        if (!$this->isValid($value)) {
            throw new ValidationException([
                'city' => [
                    'City name must be between '
                    . self::MIN_LENGTH . ' and '
                    . self::MAX_LENGTH . ' characters.'
                ]
            ]);
        }
    }

    public function isValid(string $value): bool
    {
        $name = htmlspecialchars(trim($value));
        $len = strlen($name);

        if ($len < self::MIN_LENGTH || $len > self::MAX_LENGTH) {
            return false;
        }

        return true;
    }
}
