<?php

namespace App\Domain\Subscription\ValueObjects\Email\Validator;

use App\Exceptions\ValidationException;

class EmailValidator implements EmailValidatorInterface
{
    private const MIN_LENGTH = 5;
    private const MAX_LENGTH = 254;

    /**
     * @throws ValidationException
     */
    public function assertValid(string $value): void
    {
        if (!$this->isValid($value)) {
            throw new ValidationException([
                'email' => ["Invalid email address: {$value}"]
            ]);
        }
    }

    public function isValid(string $value): bool
    {
        $email = htmlspecialchars(trim($value));

        $len = strlen($email);
        if (
            $len < self::MIN_LENGTH ||
            $len > self::MAX_LENGTH ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            return false;
        }
        return true;
    }
}
