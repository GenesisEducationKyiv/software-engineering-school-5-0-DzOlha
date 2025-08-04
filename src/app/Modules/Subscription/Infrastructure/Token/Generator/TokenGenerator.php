<?php

namespace App\Modules\Subscription\Infrastructure\Token\Generator;

use App\Modules\Subscription\Domain\ValueObjects\Token\Generator\TokenGeneratorInterface;
use Random\RandomException;

class TokenGenerator implements TokenGeneratorInterface
{
    private const TOKEN_LENGTH = 64;

    /**
     * @param positive-int|null $length
     * @throws RandomException
     */
    public function generate(?int $length = null): string
    {
        $len = ($length === null || $length < 2) ? self::TOKEN_LENGTH : $length;
        /**
         * @var positive-int $halfLen
         */
        $halfLen = (int)($len / 2);

        return bin2hex(random_bytes($halfLen));
    }
}
