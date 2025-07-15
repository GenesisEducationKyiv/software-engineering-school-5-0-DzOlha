<?php

namespace App\Modules\Notification\Application\Listeners;

use App\Modules\Email\Presentation\Interface\EmailModuleInterface;
use App\Modules\Notification\Application\Events\NotificationSubscriptionCreated;
use App\Modules\Subscription\Presentation\Interface\SubscriptionModuleInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendConfirmationEmail implements ShouldQueue
{
    public function __construct(
        private readonly EmailModuleInterface $emailModule,
        private readonly SubscriptionModuleInterface $subscriptionModule
    ) {
    }

    public function handle(NotificationSubscriptionCreated $event): void
    {
        $id = $event->subscription->getId();

        if (!$id) {
            Log::warning('Subscription ID is null');
            return;
        }

        Log::info('SubscriptionCreated event received', [
            'subscription_id' => $id
        ]);

        $emailSent = $this->emailModule->sendConfirmationEmail(
            $this->emailModule->getEmailSubscriptionEntity(
                $event->subscription->getId(),
                $event->subscription->getEmail(),
                $event->subscription->getCity(),
                $event->subscription->getFrequency(),
                $event->subscription->getConfirmationToken(),
                $event->subscription->getUnsubscribeToken()
            )
        );

        if (!$emailSent) {
            $this->subscriptionModule->deleteSubscription($id);
            Log::info(
                "Subscription ID {$id} deleted due to failed confirmation email."
            );
        }
    }
}
