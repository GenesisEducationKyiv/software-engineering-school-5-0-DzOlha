<?php

namespace App\Modules\Subscription\Domain\ValueObjects\Token\Validator;

use App\Exceptions\ValidationException;

class TokenValidator implements TokenValidatorInterface
{
    private const TOKEN_LENGTH = 64;

    public function assertValid(string $value): void
    {
        if (!$this->isValid($value)) {
            throw new ValidationException([
                'token' => ["Invalid token provided"]
            ]);
        }
    }

    public function isValid(string $value): bool
    {
        $token = trim($value);

        /**
         * Validate token length and content (only hexadecimal characters (0-9, a-f))
         */
        if (strlen($token) !== self::TOKEN_LENGTH || !ctype_xdigit($token)) {
            return false;
        }

        return true;
    }
}
