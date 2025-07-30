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
            $this->monitor->logger()->logWarn(
                'Cannot process SubscriptionConfirmed event: subscription has no ID',
                [
                    'module' => 'Notification',
                    'message' => $eventData->toArray(),
                ]
            );
            return;
        }

        $this->monitor->logger()->logInfo(
            'Valid SubscriptionConfirmed event received',
            [
                'module' => 'Notification',
                'message' => $eventData->toArray(),
            ]
        );

        SendWeatherUpdates::dispatch($subscriptionId);
    }
}
