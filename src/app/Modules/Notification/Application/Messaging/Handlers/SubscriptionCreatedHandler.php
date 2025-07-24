<?php

namespace App\Modules\Notification\Application\Messaging\Handlers;

use App\Exceptions\ValidationException;
use App\Modules\Email\Presentation\Interface\EmailModuleInterface;
use App\Modules\Notification\Application\Messaging\Messages\EventBodyMessage;
use App\Modules\Notification\Domain\Entities\NotificationSubscriptionEntity;
use App\Modules\Subscription\Presentation\Interface\SubscriptionModuleInterface;
use Illuminate\Support\Facades\Log;

class SubscriptionCreatedHandler extends EventHandler
{
    public function __construct(
        private readonly EmailModuleInterface $emailModule,
        private readonly SubscriptionModuleInterface $subscriptionModule
    ) {
    }

    /**
     * @throws ValidationException
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

        $subscription = NotificationSubscriptionEntity::fromArray($payload['subscription']);
        $id = $subscription->getId();

        if (!$id) {
            Log::warning('Subscription ID is null');
            return;
        }

        Log::info('SubscriptionCreated event received', [
            'subscription_id' => $id
        ]);

        $emailSent = $this->emailModule->sendConfirmationEmail(
            $this->emailModule->getEmailSubscriptionEntity(
                $subscription->getId(),
                $subscription->getEmail(),
                $subscription->getCity(),
                $subscription->getFrequency(),
                $subscription->getConfirmationToken(),
                $subscription->getUnsubscribeToken()
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
