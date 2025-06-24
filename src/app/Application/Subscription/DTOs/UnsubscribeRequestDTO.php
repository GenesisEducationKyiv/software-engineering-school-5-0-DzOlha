<?php

namespace App\Application\Subscription\DTOs;

use App\Domain\Subscription\ValueObjects\Token\Token;

class UnsubscribeRequestDTO
{
    public function __construct(
        public readonly Token $cancelToken
    ) {
    }
}
