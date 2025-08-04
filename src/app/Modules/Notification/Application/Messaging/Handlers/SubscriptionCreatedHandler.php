<?php

namespace App\Modules\Notification\Application\Messaging\Handlers;

use App\Exceptions\ValidationException;
use App\Modules\Email\Presentation\Interface\EmailModuleInterface;
use App\Modules\Notification\Application\Messaging\Messages\MessageBody;
use App\Modules\Notification\Domain\Entities\NotificationSubscriptionEntity;
use App\Modules\Observability\Presentation\Interface\ObservabilityModuleInterface;
use App\Modules\Subscription\Application\Messaging\Events\SubscriptionCreated;
use App\Modules\Subscription\Presentation\Interface\SubscriptionModuleInterface;

class SubscriptionCreatedHandler extends EventHandler
{
    public function __construct(
        ObservabilityModuleInterface $monitor,
        private readonly EmailModuleInterface $emailModule,
        private readonly SubscriptionModuleInterface $subscriptionModule
    ) {
        parent::__construct($monitor);
    }

    /**
     * @throws ValidationException
     * @throws \JsonException
     */
    public function handle(MessageBody $eventData): void
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
        $event = SubscriptionCreated::fromArray($payload);

        $subscription = NotificationSubscriptionEntity::fromArray($event->subscription->toArray());
        $id = $subscription->getId();

        if (!$id) {
            return;
        }

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
            $this->monitor->logger()->logInfo(
                "Subscription ID {$id} deleted due to failed confirmation email.",
                [
                    'module' => 'Notification',
                    'message' => $eventData->toArray()
                ]
            );
        }
    }
}
