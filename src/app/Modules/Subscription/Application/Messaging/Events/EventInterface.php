<?php

namespace App\Modules\Subscription\Application\Messaging\Events;

interface EventInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self;
}
