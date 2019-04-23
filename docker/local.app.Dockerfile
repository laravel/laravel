FROM php:7.3-fpm-alpine

RUN apk update \
    && apk add \
        build-base \
        libpng-dev \
        libjpeg-turbo-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-jpeg-dir=/usr/lib/ \
    && docker-php-ext-install pdo_mysql zip bcmath gd exif \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename composer

RUN apk add chromium chromium-chromedriver
