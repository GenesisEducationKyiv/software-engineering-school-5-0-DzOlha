<?php

namespace App\Modules\Subscription\Application\DTOs;

use App\Modules\Subscription\Domain\ValueObjects\Token\Token;

class UnsubscribeRequestDTO
{
    public function __construct(
        public readonly Token $cancelToken
    ) {
    }
}
