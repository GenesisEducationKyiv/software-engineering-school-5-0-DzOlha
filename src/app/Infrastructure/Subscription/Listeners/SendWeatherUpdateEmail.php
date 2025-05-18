<?php

namespace App\Infrastructure\Subscription\Listeners;

use App\Domain\Subscription\Events\SubscriptionConfirmed;
use App\Interfaces\Jobs\SendWeatherUpdates;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendWeatherUpdateEmail implements ShouldQueue
{
    public function __construct() {}

    public function handle(SubscriptionConfirmed $event): void
    {
        Log::info('SubscriptionConfirmed event received', ['subscription_id' => $event->subscription->getId()]);
        SendWeatherUpdates::dispatch($event->subscription->getId());
    }
}
