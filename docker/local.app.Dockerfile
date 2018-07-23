FROM php:7.1-alpine

RUN apk update \
    && apk add \
        # Common
        build-base \
        # Laravel
        libmcrypt-dev \
        mysql-client \
        # Node JS
        git \
        yarn \
        python \
        bash \
        libpng-dev \
    && docker-php-ext-install pdo_mysql zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename composer

RUN apk add chromium chromium-chromedriver

WORKDIR /var/www/
