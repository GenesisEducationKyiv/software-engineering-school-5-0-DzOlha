<?php

namespace App\Modules\Subscription\Application\Messaging\Events;

use App\Exceptions\ValidationException;
use App\Modules\Subscription\Domain\Entities\Subscription;

readonly class SubscriptionEvent implements EventInterface
{
    public function __construct(
        public Subscription $subscription
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'subscription' => $this->subscription->toArray()
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return SubscriptionEvent
     * @throws ValidationException
     */
    public static function fromArray(array $data): self
    {
        /**
         * @var array{
         *       id: int|null,
         *       email: string,
         *       city: array{name: string},
         *       frequency: array{id: int, name: string},
         *       status: string,
         *       confirmation_token: string|null,
         *       unsubscribe_token: string|null
         *   } $data
         */
        return new self(
            Subscription::fromArray($data)
        );
    }
}
