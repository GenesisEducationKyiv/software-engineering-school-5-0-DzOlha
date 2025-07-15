<?php

namespace App\Modules\Notification\Application\Events;

use App\Modules\Notification\Domain\Entities\NotificationSubscriptionEntity;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSubscriptionCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly NotificationSubscriptionEntity $subscription
    ) {
    }
}
