<?php

namespace App\Infrastructure\Subscription\Listeners;

use App\Domain\Subscription\Events\SubscriptionCreated;
use App\Infrastructure\Subscription\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendConfirmationEmail implements ShouldQueue
{
    public function __construct(
        private readonly EmailService $emailService
    ) {
    }

    public function handle(SubscriptionCreated $event): void
    {
        Log::info('SubscriptionCreated event received', ['subscription_id' => $event->subscription->getId()]);
        $this->emailService->sendConfirmationEmail($event->subscription);
    }
}
