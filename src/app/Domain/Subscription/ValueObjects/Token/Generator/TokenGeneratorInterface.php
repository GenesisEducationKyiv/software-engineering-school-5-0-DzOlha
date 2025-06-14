<?php

namespace App\Domain\Subscription\ValueObjects\Token\Generator;

use Random\RandomException;

interface TokenGeneratorInterface
{
    /**
     * @param positive-int|null $length
     * @throws RandomException
     */
    public function generate(?int $length = null): string;
}
