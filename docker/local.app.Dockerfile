FROM php:7.4-fpm-alpine

RUN apk update \
    && apk add \
        build-base \
        libpng-dev \
        libjpeg-turbo-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo_mysql zip bcmath gd exif \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename composer
