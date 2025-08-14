#!/bin/sh
set -e

php artisan key:generate

if [ "$INTEGRATION_MODE" = "true" ]; then
  php artisan migrate --force
fi

php artisan optimize:clear

if [ "$INTEGRATION_MODE" = "true" ]; then
  echo "⏳ Waiting for RabbitMQ to be available on rabbitmq:5672..."
  until nc -z -v -w30 rabbitmq 5672; do
    echo "⏱️  Waiting for RabbitMQ..."
    sleep 2
  done
  echo "✅ RabbitMQ is up and running"

  php artisan rabbitmq:setup
fi

exec "$@"