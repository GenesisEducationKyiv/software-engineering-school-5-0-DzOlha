<?php

namespace App\Domain\Subscription\ValueObjects;

use App\Exceptions\ValidationException;

class Email
{
    private const MIN_LENGTH = 5;
    private const MAX_LENGTH = 254;

    public function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    /**
     * @throws ValidationException
     */
    private function validate(string $email): void
    {
        $email = htmlspecialchars(trim($email));

        $len = strlen($email);
        if (
            $len < self::MIN_LENGTH ||
            $len > self::MAX_LENGTH ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            throw new ValidationException([
                'email' => ["Invalid email address: {$email}"]
            ]);
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
