FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash git unzip icu-dev libzip-dev postgresql-dev oniguruma-dev

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 app && adduser -G app -g app -s /bin/sh -D -u 1000 app
USER app

WORKDIR /var/www/html