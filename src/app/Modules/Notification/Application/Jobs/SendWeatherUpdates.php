<?php

namespace App\Application\Subscription\Jobs;

use App\Application\Subscription\Emails\EmailServiceInterface;
use App\Application\Weather\Services\WeatherServiceInterface;
use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use App\Modules\Weather\Application\DTOs\WeatherRequestDTO;
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
        WeatherServiceInterface $weatherService,
        EmailServiceInterface $emailService,
        SubscriptionRepositoryInterface $subscriptionRepository
    ): void {
        Log::info('Running weather update job', ['subscription_id' => $this->subscriptionId]);
        /**
         * @var ?Subscription $subscription
         */
        $subscription = $subscriptionRepository->findSubscriptionById($this->subscriptionId);

        if (!$subscription || !$subscription->isActive()) {
            Log::info('Skipping weather update: inactive or missing subscription', [
                'subscription_id' => $this->subscriptionId,
            ]);
            return;
        }

        Log::info('Subscription entity:', [
            'subscription' => $subscription->toArray()
        ]);

        $subscriptionId = $subscription->getId();
        if ($subscriptionId === null) {
            Log::warning('Subscription ID is null, skipping dispatch');
            return;
        }

        $weatherData = $weatherService->getCurrentWeather(
            new WeatherRequestDTO($subscription->getCity())
        );

        $sent = $emailService->sendWeatherUpdate($subscription, $weatherData);

        if ($sent) {
            $intervalMinutes = $subscription->getFrequency()->getIntervalMinutes();

            $subscriptionRepository->updateSubscriptionEmailStatus(
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
            $updated = $subscriptionRepository->updateSubscriptionEmailStatus(
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
