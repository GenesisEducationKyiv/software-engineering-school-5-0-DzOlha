FROM php:8.2.23-fpm-alpine3.20

RUN apk update && apk add --no-cache \
    build-base \
    autoconf \
    g++ \
    make \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    git \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    icu-dev \
    postgresql-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip intl \
 && pecl install redis \
 && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY ./src/composer.json ./src/composer.lock ./

RUN composer install --optimize-autoloader --no-scripts --no-interaction

COPY --chown=www-data:www-data ./src /var/www/html

COPY ./infra/dev/php/.env /var/www/html/.env

# Disable PHP version exposure
RUN echo "expose_php = Off" > /usr/local/etc/php/conf.d/security.ini

EXPOSE 9000

# Copy entrypoint into the container and set permission to execute it
COPY ./infra/dev/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
