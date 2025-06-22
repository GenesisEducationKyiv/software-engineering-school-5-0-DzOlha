<?php

namespace App\Providers;

use App\Application\Subscription\Emails\EmailServiceInterface;
use App\Application\Subscription\Emails\Mailers\MailerInterface;
use App\Application\Subscription\Emails\Services\EmailService;
use App\Application\Subscription\Listeners\SendConfirmationEmail;
use App\Application\Subscription\Listeners\SendWeatherUpdateEmail;
use App\Application\Subscription\Services\SubscriptionService;
use App\Application\Subscription\Services\SubscriptionServiceInterface;
use App\Application\Subscription\Utils\Builders\SubscriptionLinkBuilderInterface;
use App\Application\Weather\HttpClient\Decorators\HttpClientWithLogger;
use App\Application\Weather\HttpClient\HttpClientInterface;
use App\Application\Weather\Services\WeatherService;
use App\Application\Weather\Services\WeatherServiceInterface;
use App\Domain\Subscription\Events\SubscriptionConfirmed;
use App\Domain\Subscription\Events\SubscriptionCreated;
use App\Domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use App\Domain\Subscription\ValueObjects\Token\Factory\TokenFactory;
use App\Domain\Subscription\ValueObjects\Token\Factory\TokenFactoryInterface;
use App\Domain\Subscription\ValueObjects\Token\Generator\TokenGeneratorInterface;
use App\Domain\Weather\Repositories\Chain\Builder\WeatherChainBuilderInterface;
use App\Domain\Weather\Repositories\WeatherRepositoryInterface;
use App\Infrastructure\Subscription\Emails\Mailers\LaravelMailer;
use App\Infrastructure\Subscription\Repositories\SubscriptionRepository;
use App\Infrastructure\Subscription\Token\Generator\TokenGenerator;
use App\Infrastructure\Subscription\Utils\Builders\SubscriptionLinkBuilder;
use App\Infrastructure\Weather\HttpClient\HttpClient;
use App\Infrastructure\Weather\HttpClient\Logger\FileHttpLogger;
use App\Infrastructure\Weather\Repositories\Chain\Builder\WeatherChainBuilder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TokenGeneratorInterface::class, TokenGenerator::class);
        $this->app->singleton(TokenFactoryInterface::class, TokenFactory::class);

        $this->app->singleton(HttpClientInterface::class, function (Application $app) {
            return new HttpClientWithLogger(
                $app->make(HttpClient::class),
                $app->make(FileHttpLogger::class)
            );
        });

        $this->app->singleton(WeatherChainBuilderInterface::class, WeatherChainBuilder::class);

        $this->app->singleton(WeatherRepositoryInterface::class, function (Application $app) {
            return $app->make(WeatherChainBuilderInterface::class)->build();
        });

        $this->app->singleton(WeatherServiceInterface::class, WeatherService::class);

        $this->app->singleton(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        $this->app->singleton(SubscriptionServiceInterface::class, SubscriptionService::class);

        $this->app->singleton(MailerInterface::class, LaravelMailer::class);
        $this->app->singleton(EmailServiceInterface::class, EmailService::class);

        $this->app->singleton(SubscriptionLinkBuilderInterface::class, SubscriptionLinkBuilder::class);
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
