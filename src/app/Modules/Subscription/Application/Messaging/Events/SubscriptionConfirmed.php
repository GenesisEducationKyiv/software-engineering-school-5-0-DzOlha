<?php

namespace App\Modules\Subscription\Application\Messaging\Events;

readonly class SubscriptionConfirmed implements EventInterface
{
    public function __construct(
        public ?int $subscriptionId
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'subscription_id' => $this->subscriptionId
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return SubscriptionConfirmed
     */
    public static function fromArray(array $data): self
    {
        /**
         * @var array{
         *       subscription_id: int|null
         *   } $data
         */
        return new self(
            $data['subscription_id']
        );
    }
}
