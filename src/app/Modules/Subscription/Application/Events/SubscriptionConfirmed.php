<?php

namespace App\Modules\Subscription\Application\Events;

use App\Modules\Subscription\Domain\Entities\Subscription;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmed
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Subscription $subscription
    ) {
    }
}
