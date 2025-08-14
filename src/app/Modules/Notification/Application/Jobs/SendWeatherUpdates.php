<?php

namespace App\Modules\Notification\Application\Jobs;

use App\Modules\Notification\Domain\Entities\NotificationSubscriptionEntity;
use App\Modules\Email\Presentation\Interface\EmailModuleInterface;
use App\Modules\Observability\Presentation\Interface\ObservabilityModuleInterface;
use App\Modules\Subscription\Presentation\Interface\SubscriptionModuleInterface;
use App\Modules\Weather\Presentation\Interface\WeatherModuleInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWeatherUpdates implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $subscriptionId,
        private readonly int $retryMinutes = 60
    ) {
    }

    public function handle(
        WeatherModuleInterface $weatherModule,
        EmailModuleInterface $emailModule,
        SubscriptionModuleInterface $subscriptionModule,
        ObservabilityModuleInterface $monitor
    ): void {
        $subscriptionExternal = $subscriptionModule->findSubscriptionEntityById($this->subscriptionId);

        if (!$subscriptionExternal || !$subscriptionExternal->isActive()) {
            return;
        }

        $subscription = new NotificationSubscriptionEntity(
            id: $subscriptionExternal->getId(),
            email: $subscriptionExternal->getEmail()->getValue(),
            city: $subscriptionExternal->getCity()->getName(),
            frequency: $subscriptionExternal->getFrequency()->getName(),
            confirmationToken: $subscriptionExternal->getConfirmationToken()?->getValue(),
            unsubscribeToken: $subscriptionExternal->getUnsubscribeToken()?->getValue(),
            isActive: $subscriptionExternal->isActive(),
            intervalMinutes: $subscriptionExternal->getFrequency()->getIntervalMinutes()
        );

        $subscriptionId = $subscription->getId();
        if ($subscriptionId === null) {
            return;
        }

        $weatherData = $weatherModule->getCurrentWeather($subscription->getCity());

        $sent = $emailModule->sendWeatherUpdate(
            $emailModule->getEmailSubscriptionEntity(
                $subscription->getId(),
                $subscription->getEmail(),
                $subscription->getCity(),
                $subscription->getFrequency(),
                $subscription->getConfirmationToken(),
                $subscription->getUnsubscribeToken()
            ),
            $emailModule->getEmailWeatherEntity(
                $weatherData->getTemperature(),
                $weatherData->getHumidity(),
                $weatherData->getDescription()
            )
        );

        if ($sent) {
            /**
             * @var int $intervalMinutes
             */
            $intervalMinutes = $subscription->getIntervalMinutes();

            $subscriptionModule->updateSubscriptionEmailStatus(
                $subscriptionId,
                $intervalMinutes
            );

            /**
             * Schedule the next update
             */
            self::dispatch($subscriptionId)->delay(now()->addMinutes($intervalMinutes));
        } else {
            $monitor->logger()->logError(
                'Failed to send weather update email',
                [
                    'module' => $this->getModuleName(),
                    'subscription_id' => $subscription->getId()
                ]
            );

            /**
             * Update with error status and retry in 60 minutes
             */
            $updated = $subscriptionModule->updateSubscriptionEmailStatus(
                $subscriptionId,
                $this->retryMinutes,
                false
            );
            if (!$updated) {
                $monitor->logger()->logError(
                    'Failed to update subscription email status',
                    [
                        'module' => $this->getModuleName(),
                        'subscription_id' => $subscription->getId()
                    ]
                );
            }

            /**
             * Retry
             */
            self::dispatch($subscriptionId)->delay(now()->addMinutes($this->retryMinutes));
        }
    }

    private function getModuleName(): string
    {
        return 'Notification';
    }
}
