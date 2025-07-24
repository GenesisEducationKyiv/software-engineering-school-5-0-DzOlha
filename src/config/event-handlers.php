<?php

use App\Modules\Notification\Application\Messaging\Handlers\SubscriptionConfirmedHandler;
use App\Modules\Notification\Application\Messaging\Handlers\SubscriptionCreatedHandler;
use App\Modules\Subscription\Application\Messaging\Events\SubscriptionConfirmed;
use App\Modules\Subscription\Application\Messaging\Events\SubscriptionCreated;

return [
    SubscriptionCreated::class => [
        SubscriptionCreatedHandler::class
    ],
    SubscriptionConfirmed::class => [
        SubscriptionConfirmedHandler::class
    ]
];
