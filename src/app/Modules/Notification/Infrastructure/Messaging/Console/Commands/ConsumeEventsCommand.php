<?php

namespace App\Modules\Notification\Infrastructure\Messaging\Console\Commands;

use App\Modules\Notification\Application\Messaging\Consumers\EventConsumerInterface;
use Illuminate\Console\Command;

class ConsumeEventsCommand extends Command
{
    protected $signature = 'rabbitmq-events:consume {queue=subscription.events}';
    protected $description = 'Consume domain events from message queue';

    public function handle(EventConsumerInterface $consumer): void
    {
        /**
         * @var string $queueName
         */
        $queueName = $this->argument('queue');

        $this->info("Starting to consume events from queue: {$queueName}");
        $consumer->consume($queueName);
    }
}
