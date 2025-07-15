<?php

namespace App\Providers;

use App\Modules\Email\Application\EmailServiceInterface;
use App\Modules\Email\Application\Mailers\MailerInterface;
use App\Modules\Email\Application\Services\EmailService;
use App\Modules\Email\Application\Utils\Builders\SubscriptionLinkBuilderInterface;
use App\Modules\Email\Infrastructure\Mailers\LaravelMailer;
use App\Modules\Email\Infrastructure\Utils\Builders\SubscriptionLinkBuilder;
use App\Modules\Email\Presentation\Interface\EmailModule;
use App\Modules\Email\Presentation\Interface\EmailModuleInterface;
use App\Modules\Notification\Application\Events\NotificationSubscriptionConfirmed;
use App\Modules\Notification\Application\Events\NotificationSubscriptionCreated;
use App\Modules\Notification\Application\Listeners\Forwarders\SubscriptionConfirmedForwarder;
use App\Modules\Notification\Application\Listeners\Forwarders\SubscriptionCreatedForwarder;
use App\Modules\Notification\Application\Listeners\SendConfirmationEmail;
use App\Modules\Notification\Application\Listeners\SendWeatherUpdateEmail;
use App\Modules\Subscription\Application\Events\SubscriptionConfirmed;
use App\Modules\Subscription\Application\Events\SubscriptionCreated;
use App\Modules\Subscription\Application\Services\SubscriptionService;
use App\Modules\Subscription\Application\Services\SubscriptionServiceInterface;
use App\Modules\Subscription\Domain\Repositories\SubscriptionRepositoryInterface;
use App\Modules\Subscription\Domain\ValueObjects\Token\Factory\TokenFactory;
use App\Modules\Subscription\Domain\ValueObjects\Token\Factory\TokenFactoryInterface;
use App\Modules\Subscription\Domain\ValueObjects\Token\Generator\TokenGeneratorInterface;
use App\Modules\Subscription\Infrastructure\Repositories\SubscriptionRepository;
use App\Modules\Subscription\Infrastructure\Token\Generator\TokenGenerator;
use App\Modules\Subscription\Presentation\Interface\SubscriptionModule;
use App\Modules\Subscription\Presentation\Interface\SubscriptionModuleInterface;
use App\Modules\Weather\Application\HttpClient\Decorators\HttpClientWithLogger;
use App\Modules\Weather\Application\HttpClient\HttpClientInterface;
use App\Modules\Weather\Application\Services\WeatherService;
use App\Modules\Weather\Application\Services\WeatherServiceInterface;
use App\Modules\Weather\Domain\Repositories\Cache\Monitor\WeatherCacheMonitorInterface;
use App\Modules\Weather\Domain\Repositories\Chain\Builder\WeatherChainBuilderInterface;
use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;
use App\Modules\Weather\Infrastructure\HttpClient\HttpClient;
use App\Modules\Weather\Infrastructure\HttpClient\Logger\FileHttpLogger;
use App\Modules\Weather\Infrastructure\Repositories\Cache\Monitor\PrometheusWeatherCacheMonitor;
use App\Modules\Weather\Infrastructure\Repositories\Cache\WeatherRepositoryCache;
use App\Modules\Weather\Infrastructure\Repositories\Chain\Builder\WeatherChainBuilder;
use App\Modules\Weather\Presentation\Interface\WeatherModule;
use App\Modules\Weather\Presentation\Interface\WeatherModuleInterface;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Adapter;
use Prometheus\Storage\Redis;

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

        $this->app->bind(Adapter::class, function () {
            return new Redis([
                'host' => config('cache.stores.redis.host'),
                'port' => config('cache.stores.redis.port')
            ]);
        });

        $this->app->singleton(CollectorRegistry::class, function (Application $app) {
            /**
             * @var Adapter $adapter
             */
            $adapter = $app->make(Adapter::class);
            return new CollectorRegistry($adapter);
        });

        $this->app->singleton(WeatherCacheMonitorInterface::class, PrometheusWeatherCacheMonitor::class);

        $this->app->singleton(WeatherRepositoryInterface::class, function (Application $app) {
            $apiRepository = $app->make(WeatherChainBuilderInterface::class)->build();
            /**
             * @var WeatherCacheMonitorInterface $prometheusMonitor
             */
            $prometheusMonitor = $app->make(WeatherCacheMonitorInterface::class);

            /**
             * @var Repository $cacheContract
             */
            $cacheContract = $app->make(Repository::class);

            return new WeatherRepositoryCache(
                $apiRepository,
                $prometheusMonitor,
                $cacheContract
            );
        });

        $this->app->singleton(WeatherServiceInterface::class, WeatherService::class);

        $this->app->singleton(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        $this->app->singleton(SubscriptionServiceInterface::class, SubscriptionService::class);

        $this->app->singleton(MailerInterface::class, LaravelMailer::class);
        $this->app->singleton(EmailServiceInterface::class, EmailService::class);

        $this->app->singleton(SubscriptionLinkBuilderInterface::class, SubscriptionLinkBuilder::class);

        $this->app->singleton(SubscriptionModuleInterface::class, SubscriptionModule::class);
        $this->app->singleton(WeatherModuleInterface::class, WeatherModule::class);
        $this->app->singleton(EmailModuleInterface::class, EmailModule::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            SubscriptionCreated::class,
            SubscriptionCreatedForwarder::class
        );
        Event::listen(
            SubscriptionConfirmed::class,
            SubscriptionConfirmedForwarder::class
        );

        Event::listen(
            NotificationSubscriptionCreated::class,
            SendConfirmationEmail::class
        );
        Event::listen(
            NotificationSubscriptionConfirmed::class,
            SendWeatherUpdateEmail::class
        );
    }
}
