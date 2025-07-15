<?php

namespace App\Modules\Subscription\Application\DTOs;

use App\Modules\Subscription\Domain\ValueObjects\Token\Token;

class ConfirmSubscriptionRequestDTO
{
    public function __construct(
        public readonly Token $confirmationToken
    ) {
    }
}
