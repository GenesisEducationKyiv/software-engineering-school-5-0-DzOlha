<?php

namespace App\Modules\Subscription\Application\Messaging\Generator;

interface EventKeyGeneratorInterface
{
    public function generateUniqueKey(): string;
}
