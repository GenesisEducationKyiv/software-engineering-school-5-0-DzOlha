FROM php:8.2.23-fpm-alpine3.20

WORKDIR /var/www/html

RUN apk add --no-cache \
    postgresql-dev \
    libpq \
    && docker-php-ext-install pdo pdo_pgsql

ENTRYPOINT ["php", "/var/www/html/artisan"]
