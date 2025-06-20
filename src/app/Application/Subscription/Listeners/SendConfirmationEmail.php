<?php

namespace App\Application\Subscription\Listeners;

use App\Application\Subscription\Emails\EmailServiceInterface;
use App\Domain\Subscription\Events\SubscriptionCreated;
use App\Domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendConfirmationEmail implements ShouldQueue
{
    public function __construct(
        private readonly EmailServiceInterface $emailService,
        private readonly SubscriptionRepositoryInterface $repository
    ) {
    }

    public function handle(SubscriptionCreated $event): void
    {
        $id = $event->subscription->getId();

        if (!$id) {
            Log::warning('Subscription ID is null');
            return;
        }

        Log::info('SubscriptionCreated event received', [
            'subscription_id' => $id
        ]);

        $emailSent = $this->emailService->sendConfirmationEmail($event->subscription);

        if (!$emailSent) {
            $this->repository->delete($id);
            Log::info(
                "Subscription ID {$id} deleted due to failed confirmation email."
            );
        }
    }
}
