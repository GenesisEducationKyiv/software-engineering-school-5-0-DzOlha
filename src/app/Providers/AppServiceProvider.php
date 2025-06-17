<?php

namespace App\Providers;

use App\Application\Mail\MailerInterface;
use App\Application\Subscription\Services\EmailServiceInterface;
use App\Application\Subscription\Services\SubscriptionServiceInterface;
use App\Application\Subscription\Utils\Builders\SubscriptionLinkBuilderInterface;
use App\Application\Weather\Services\WeatherServiceInterface;
use App\Domain\Subscription\Events\SubscriptionConfirmed;
use App\Domain\Subscription\Events\SubscriptionCreated;
use App\Domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Services\SubscriptionService;
use App\Domain\Weather\Repositories\WeatherRepositoryInterface;
use App\Domain\Weather\Services\WeatherService;
use App\Infrastructure\Mail\LaravelMailer;
use App\Infrastructure\Subscription\Listeners\SendConfirmationEmail;
use App\Infrastructure\Subscription\Listeners\SendWeatherUpdateEmail;
use App\Infrastructure\Subscription\Repositories\SubscriptionRepository;
use App\Infrastructure\Subscription\Services\EmailService;
use App\Infrastructure\Subscription\Utils\Builders\SubscriptionLinkBuilder;
use App\Infrastructure\Weather\ExternalServices\WeatherApiService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WeatherRepositoryInterface::class, WeatherApiService::class);
        $this->app->bind(WeatherServiceInterface::class, WeatherService::class);

        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        $this->app->bind(SubscriptionServiceInterface::class, SubscriptionService::class);

        $this->app->bind(MailerInterface::class, LaravelMailer::class);
        $this->app->bind(EmailServiceInterface::class, EmailService::class);

        $this->app->bind(SubscriptionLinkBuilderInterface::class, SubscriptionLinkBuilder::class);
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
