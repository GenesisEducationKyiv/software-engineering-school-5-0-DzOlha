<?php

namespace App\Modules\Subscription\Infrastructure\Messaging\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class SetupRabbitMQCommand extends Command
{
    protected $signature = 'rabbitmq:setup';
    protected $description = 'Setup RabbitMQ exchanges and queues';

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $config = [
            'host' => config('rabbitmq.host'),
            'port' => config('rabbitmq.port'),
            'user' => config('rabbitmq.user'),
            'password' => config('rabbitmq.password'),
            'vhost' => config('rabbitmq.vhost'),
        ];

        /**
         * @var array{
         *      host: string,
         *      port: int,
         *      user: string,
         *      password: string,
         *      vhost: string
         *  } $config
         */
        $connection = new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config['vhost']
        );

        $channel = $connection->channel();

        // Setup exchanges for each module
        $exchanges = [
            'subscription.events' => 'topic',
        ];

        foreach ($exchanges as $exchange => $type) {
            $channel->exchange_declare($exchange, $type, false, true, false);
            $this->info("Created exchange: {$exchange}");
        }

        // Setup queues and bindings
        $queueBindings = [
            'subscription.events' => [
                'exchange' => 'subscription.events',
                'routing_key' => 'subscription.*',
            ]
        ];

        foreach ($queueBindings as $queue => $binding) {
            $channel->queue_declare(
                $queue,
                false,
                true,
                false,
                false
            );
            $channel->queue_bind($queue, $binding['exchange'], $binding['routing_key']);
            $this->info("Created queue: {$queue} bound to {$binding['exchange']}");
        }

        $channel->close();
        $connection->close();

        $this->info('RabbitMQ setup completed successfully!');
    }
}
