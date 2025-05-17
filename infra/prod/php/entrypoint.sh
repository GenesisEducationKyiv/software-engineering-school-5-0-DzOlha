#!/bin/sh
set -e

# generate application key
php artisan key:generate

# Run database migrations (forced in production)
php artisan migrate --force

# Clear and optimize caches
php artisan optimize:clear    # Clears all caches: config, route, view, and app
php artisan optimize          # Rebuilds caches for performance

# Execute the CMD specified in the Dockerfile
exec "$@"