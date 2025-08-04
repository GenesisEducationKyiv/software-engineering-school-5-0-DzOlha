<?php

namespace App\Providers;

use App\Modules\Notification\Application\Messaging\Consumers\EventConsumer;
use App\Modules\Notification\Application\Messaging\Consumers\EventConsumerInterface;
use App\Modules\Notification\Application\Messaging\Executors\EventHandlerExecutorInterface;
use App\Modules\Notification\Application\Messaging\Handlers\Registry\ConfigurationEventHandlerRegistry;
use App\Modules\Notification\Application\Messaging\Handlers\Registry\EventHandlerRegistryInterface;
use App\Modules\Notification\Application\Repositories\ProcessedEventsRepositoryInterface;
use App\Modules\Notification\Infrastructure\Messaging\Console\Commands\ConsumeEventsCommand;
use App\Modules\Notification\Infrastructure\Messaging\Executors\EventHandlerExecutor;
use App\Modules\Notification\Infrastructure\Repositories\ProcessedEventsRepository;
use App\Modules\Subscription\Application\Messaging\Brokers\MessageBrokerInterface;
use App\Modules\Subscription\Application\Messaging\Generator\EventKeyGenerator;
use App\Modules\Subscription\Application\Messaging\Generator\EventKeyGeneratorInterface;
use App\Modules\Subscription\Application\Messaging\Publishers\EventPublisher;
use App\Modules\Subscription\Application\Messaging\Publishers\EventPublisherInterface;
use App\Modules\Subscription\Application\Messaging\Routing\ModuleBasedRoutingStrategy;
use App\Modules\Subscription\Application\Messaging\Routing\RoutingStrategyInterface;
use App\Modules\Subscription\Infrastructure\Messaging\Broker\RabbitMQBroker;
use App\Modules\Subscription\Infrastructure\Messaging\Console\Commands\SetupRabbitMQCommand;
use Illuminate\Support\ServiceProvider;

class EventBusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MessageBrokerInterface::class, function ($app) {
            return new RabbitMQBroker([
                'host' => config('rabbitmq.host'),
                'port' => config('rabbitmq.port'),
                'user' => config('rabbitmq.user'),
                'password' => config('rabbitmq.password'),
                'vhost' => config('rabbitmq.vhost'),
            ]);
        });

        $this->app->singleton(RoutingStrategyInterface::class, ModuleBasedRoutingStrategy::class);

        $this->app->singleton(EventPublisherInterface::class, EventPublisher::class);
        $this->app->singleton(EventConsumerInterface::class, EventConsumer::class);

        $this->app->singleton(EventHandlerRegistryInterface::class, function ($app) {
            return new ConfigurationEventHandlerRegistry(
                config('event-handlers', [])
            );
        });

        $this->app->singleton(EventHandlerExecutorInterface::class, EventHandlerExecutor::class);

        $this->app->singleton(EventKeyGeneratorInterface::class, EventKeyGenerator::class);
        $this->app->singleton(ProcessedEventsRepositoryInterface::class, ProcessedEventsRepository::class);
    }

    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/event-handlers.php' => config_path('event-handlers.php'),
        ], 'event-handlers');

        $this->commands([
            ConsumeEventsCommand::class,
            SetupRabbitMQCommand::class,
        ]);
    }
}
