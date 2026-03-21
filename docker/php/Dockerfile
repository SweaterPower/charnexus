FROM php:8.5-fpm-alpine

WORKDIR /var/www/html

RUN apk update && apk add --no-cache \
    git \
    zlib-dev \
    icu-dev \
    g++ \
    autoconf \
    make \
    unzip \
    postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY --link \
    --from=ghcr.io/symfony-cli/symfony-cli:latest \
    /usr/local/bin/symfony /usr/local/bin/symfony

EXPOSE 9000
