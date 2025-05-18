<?php

namespace App\Providers;

use App\Domain\Subscription\Events\SubscriptionConfirmed;
use App\Domain\Subscription\Events\SubscriptionCreated;
use App\Domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use App\Domain\Weather\Repositories\WeatherRepositoryInterface;
use App\Infrastructure\Subscription\Listeners\SendConfirmationEmail;
use App\Infrastructure\Subscription\Listeners\SendWeatherUpdateEmail;
use App\Infrastructure\Subscription\Repositories\SubscriptionRepository;
use App\Infrastructure\Weather\ExternalServices\WeatherApiService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WeatherRepositoryInterface::class, WeatherApiService::class);
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            SubscriptionCreated::class,
            SendConfirmationEmail::class
        );

        Event::listen(
            SubscriptionConfirmed::class,
            SendWeatherUpdateEmail::class
        );
    }
}
