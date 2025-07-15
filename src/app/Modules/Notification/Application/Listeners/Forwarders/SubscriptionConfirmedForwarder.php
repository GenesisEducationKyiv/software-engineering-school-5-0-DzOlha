<?php

namespace App\Modules\Notification\Application\Listeners\Forwarders;

use App\Modules\Notification\Application\Events\NotificationSubscriptionConfirmed;
use App\Modules\Notification\Domain\Entities\NotificationSubscriptionEntity;
use App\Modules\Subscription\Application\Events\SubscriptionConfirmed;

class SubscriptionConfirmedForwarder
{
    public function handle(SubscriptionConfirmed $event): void
    {
        NotificationSubscriptionConfirmed::dispatch(
            new NotificationSubscriptionEntity(
                id: $event->subscription->getId(),
                email: $event->subscription->getEmail()->getValue(),
                city: $event->subscription->getCity()->getName(),
                frequency: $event->subscription->getFrequency()->getName(),
                confirmationToken: $event->subscription->getConfirmationToken()?->getValue(),
                unsubscribeToken: $event->subscription->getUnsubscribeToken()?->getValue(),
                isActive: $event->subscription->isActive(),
                intervalMinutes: $event->subscription->getFrequency()->getIntervalMinutes()
            )
        );
    }
}
