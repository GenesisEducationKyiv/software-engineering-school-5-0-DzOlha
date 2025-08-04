<?php

namespace App\Modules\Notification\Application\Jobs;

use App\Modules\Notification\Domain\Entities\NotificationSubscriptionEntity;
use App\Modules\Email\Presentation\Interface\EmailModuleInterface;
use App\Modules\Subscription\Presentation\Interface\SubscriptionModuleInterface;
use App\Modules\Weather\Presentation\Interface\WeatherModuleInterface;
use App\Modules\Subscription\Domain\Entities\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        SubscriptionModuleInterface $subscriptionModule
    ): void {
        Log::info('Running weather update job', ['subscription_id' => $this->subscriptionId]);

        $subscriptionExternal = $subscriptionModule->findSubscriptionEntityById($this->subscriptionId);

        if (!$subscriptionExternal || !$subscriptionExternal->isActive()) {
            Log::info('Skipping weather update: inactive or missing subscription', [
                'subscription_id' => $this->subscriptionId,
            ]);
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
            Log::warning('Subscription ID is null, skipping dispatch');
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
            Log::error('Failed to send weather update', [
                'subscription_id' => $subscription->getId()
            ]);

            /**
             * Update with error status and retry in 60 minutes
             */
            $updated = $subscriptionModule->updateSubscriptionEmailStatus(
                $subscriptionId,
                $this->retryMinutes,
                false
            );
            if (!$updated) {
                Log::error('Failed to update subscription email status', [
                'subscription_id' => $subscription->getId()
                ]);
            }

            /**
             * Retry
             */
            self::dispatch($subscriptionId)->delay(now()->addMinutes($this->retryMinutes));
        }
    }
}
