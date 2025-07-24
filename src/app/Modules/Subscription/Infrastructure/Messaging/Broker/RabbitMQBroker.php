<?php

namespace App\Modules\Subscription\Infrastructure\Messaging\Broker;

use App\Modules\Subscription\Application\Messaging\Brokers\MessageBrokerInterface;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQBroker implements MessageBrokerInterface
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    /**
     * @throws \Exception
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        /**
         * @var array{
         *     host: string,
         *     port: int,
         *     user: string,
         *     password: string,
         *     vhost: string
         * } $config
         */
        $this->connection = new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config['vhost']
        );
        $this->channel = $this->connection->channel();
    }

    /**
     * @param string $exchange
     * @param string $routingKey
     * @param array<string, mixed> $message
     * @param array<string, mixed> $headers
     * @return void
     */
    public function publish(
        string $exchange,
        string $routingKey,
        array $message,
        array $headers = []
    ): void {
        $encodedMessage = json_encode($message);
        if (!$encodedMessage) {
            $encodedMessage = '';
        }

        $amqpMessage = new AMQPMessage(
            $encodedMessage,
            array_merge([
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'message_id' => $message['event_id'] ?? uniqid(),
                'timestamp' => time(),
            ], $headers)
        );

        $this->channel->basic_publish($amqpMessage, $exchange, $routingKey);
    }

    public function consume(string $queue, callable $callback): void
    {
        $this->channel->queue_declare(
            $queue,
            false,
            true,
            false,
            false
        );
        $this->channel->basic_qos(0, 1, null);

        $amqpCallback = function (AMQPMessage $msg) use ($queue, $callback) {
            try {
                /**
                 * @param array{
                 *        event_id: string,
                 *        event_type: string,
                 *        payload: array<string, mixed>
                 *   } $messageData
                 */
                $messageData = json_decode($msg->getBody(), true);
                $result = $callback($messageData);

                if ($result === true) {
                    $msg->ack();
                } else {
                    $msg->nack(false, true); // Requeue for retry
                }
            } catch (\Exception $e) {
                Log::error('Message processing failed', [
                    'error' => $e->getMessage(),
                    'queue' => $queue,
                ]);
                $msg->nack(false, true);
            }
        };

        $this->channel->basic_consume(
            $queue,
            '',
            false,
            false,
            false,
            false,
            $amqpCallback
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
