<?php

namespace App\Modules\Notification\Application\Messaging\Handlers;

use App\Exceptions\ValidationException;
use App\Modules\Notification\Application\Jobs\SendWeatherUpdates;
use App\Modules\Notification\Application\Messaging\Messages\EventBodyMessage;
use Illuminate\Support\Facades\Log;

class SubscriptionConfirmedHandler extends EventHandler
{
    /**
     * @throws ValidationException
     */
    public function handle(EventBodyMessage $eventData): void
    {
        /**
         * @var array{
         *     subscription: array{
         *          id: int|null,
         *          email: string,
         *          city: array{name: string},
         *          frequency: array{id: int, name: string},
         *          status: string,
         *          confirmation_token: string|null,
         *          unsubscribe_token: string|null
         *     }
         * } $payload
         */
        $payload = $eventData->getPayload();
        $subscriptionId = $payload['subscription']['id'];

        if ($subscriptionId === null) {
            Log::error('Cannot dispatch SendWeatherUpdates: subscription has no ID');
            return;
        }

        Log::info(
            'SubscriptionConfirmed event received',
            ['subscription_id' => $subscriptionId]
        );

        SendWeatherUpdates::dispatch($subscriptionId);
    }
}
