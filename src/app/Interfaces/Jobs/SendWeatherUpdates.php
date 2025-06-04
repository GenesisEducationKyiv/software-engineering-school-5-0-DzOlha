<?php

namespace App\Interfaces\Jobs;

use App\Application\Weather\DTOs\WeatherRequestDTO;
use App\Domain\Subscription\Entities\Subscription;
use App\Domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use App\Domain\Weather\Services\WeatherService;
use App\Infrastructure\Subscription\Models\SubscriptionEmail;
use App\Infrastructure\Subscription\Services\EmailService;
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
        private int $subscriptionId
    ) {
    }

    public function handle(
        WeatherService $weatherService,
        EmailService $emailService,
        SubscriptionRepositoryInterface $subscriptionRepository
    ): void {
        Log::info('Running weather update job', ['subscription_id' => $this->subscriptionId]);
        /**
         * @var Subscription $subscription
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

        try {
            $weatherData = $weatherService->getCurrentWeather(
                new WeatherRequestDTO($subscription->getCity())
            );

            $emailService->sendWeatherUpdate($subscription, $weatherData);

            $intervalMinutes = $subscription->getFrequency()->getIntervalMinutes();

            $this->updateSubscriptionEmailStatus($subscription->getId(), 'success', $intervalMinutes);

            /**
             * Schedule the next update
             */
            self::dispatch($subscription->getId())->delay(now()->addMinutes($intervalMinutes));
        } catch (\Throwable $e) {
            Log::error('Failed to send weather update', [
                'subscription_id' => $subscription->getId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            /**
             * Update with error status and retry in 60 minutes
             */
            $this->updateSubscriptionEmailStatus($subscription->getId(), 'error', 60);

            /**
             * Retry
             */
            self::dispatch($subscription->getId())->delay(now()->addMinutes(60));
        }
    }

    private function updateSubscriptionEmailStatus(int $subscriptionId, string $status, int $intervalMinutes): void
    {
        $now = now();
        $nextScheduled = $now->addMinutes($intervalMinutes);

        SubscriptionEmail::updateOrInsert(
            ['subscription_id' => $subscriptionId],
            [
                'last_sent_at' => $now,
                'next_scheduled_at' => $nextScheduled,
                'status' => $status,
                'updated_at' => $now,
            ]
        );
    }
}
