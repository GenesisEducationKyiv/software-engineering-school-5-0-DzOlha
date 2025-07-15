#!/bin/sh
set -e

php artisan key:generate

if [ "$RUN_MIGRATIONS" = "true" ]; then
  php artisan migrate --force
fi

php artisan optimize:clear

exec "$@"