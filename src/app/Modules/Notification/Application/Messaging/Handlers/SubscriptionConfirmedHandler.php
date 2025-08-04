<?php

namespace App\Modules\Notification\Application\Messaging\Handlers;

use App\Exceptions\ValidationException;
use App\Modules\Notification\Application\Jobs\SendWeatherUpdates;
use App\Modules\Notification\Application\Messaging\Messages\EventBodyMessage;

class SubscriptionConfirmedHandler extends EventHandler
{
    /**
     * @throws ValidationException
     * @throws \JsonException
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
            return;
        }

        SendWeatherUpdates::dispatch($subscriptionId);
    }
}
