#!/bin/sh
set -e

# generate application key
php artisan key:generate

# Run database migrations (forced)
php artisan migrate --force

# Clear and optimize caches
php artisan optimize:clear    # Clears all caches: config, route, view, and app

echo "⏳ Waiting for RabbitMQ to be available on rabbitmq:5672..."
  until nc -z -v -w30 rabbitmq 5672; do
    echo "⏱️  Waiting for RabbitMQ..."
    sleep 2
  done
echo "✅ RabbitMQ is up and running"
php artisan rabbitmq:setup

# Execute the CMD specified in the Dockerfile
exec "$@"