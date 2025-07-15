<?php

namespace App\Modules\Notification\Application\Listeners;

use App\Modules\Notification\Application\Events\NotificationSubscriptionConfirmed;
use App\Modules\Notification\Application\Jobs\SendWeatherUpdates;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendWeatherUpdateEmail implements ShouldQueue
{
    public function __construct()
    {
    }

    public function handle(NotificationSubscriptionConfirmed $event): void
    {
        $subscriptionId = $event->subscription->getId();

        if ($subscriptionId === null) {
            Log::error('Cannot dispatch SendWeatherUpdates: subscription has no ID');
            return;
        }

        Log::info('SubscriptionConfirmed event received', ['subscription_id' => $subscriptionId]);
        SendWeatherUpdates::dispatch($subscriptionId);
    }
}
