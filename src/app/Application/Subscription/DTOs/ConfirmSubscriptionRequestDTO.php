<?php

namespace App\Application\Subscription\DTOs;

use App\Domain\Subscription\ValueObjects\Token;

class ConfirmSubscriptionRequestDTO
{
    public function __construct(
        public readonly Token $confirmationToken
    ) {
    }
}
