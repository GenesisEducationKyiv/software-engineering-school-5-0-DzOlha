<?php

namespace App\Modules\Subscription\Application\Messaging\Generator;

use Symfony\Component\Uid\Uuid;

class EventKeyGenerator implements EventKeyGeneratorInterface
{
    public function generateUniqueKey(): string
    {
        return Uuid::v4()->toRfc4122();
    }
}
