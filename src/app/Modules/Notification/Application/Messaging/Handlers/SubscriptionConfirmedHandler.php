<?php

namespace App\Modules\Notification\Application\Messaging\Handlers;

use App\Modules\Notification\Application\Jobs\SendWeatherUpdates;
use App\Modules\Notification\Application\Messaging\Messages\MessageBody;
use App\Modules\Subscription\Application\Messaging\Events\SubscriptionConfirmed;
use Illuminate\Support\Facades\Log;

class SubscriptionConfirmedHandler extends EventHandler
{
    public function handle(MessageBody $eventData): void
    {
        /**
         * @var array{
         *     subscription_id: int|null
         * } $payload
         */
        $payload = $eventData->getPayload();
        $event = SubscriptionConfirmed::fromArray($payload);

        $subscriptionId = $event->subscriptionId;

        if ($subscriptionId === null) {
            return;
        }

        SendWeatherUpdates::dispatch($subscriptionId);
    }
}
