<?php

namespace App\Infrastructure\Subscription\Listeners;

use App\Domain\Subscription\Events\SubscriptionConfirmed;
use App\Interfaces\Jobs\SendWeatherUpdates;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendWeatherUpdateEmail implements ShouldQueue
{
    public function __construct()
    {
    }

    public function handle(SubscriptionConfirmed $event): void
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
