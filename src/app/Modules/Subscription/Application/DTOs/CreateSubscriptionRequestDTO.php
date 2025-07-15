<?php

namespace App\Application\Subscription\DTOs;

class CreateSubscriptionRequestDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $city,
        public readonly string $frequency
    ) {
    }
}
