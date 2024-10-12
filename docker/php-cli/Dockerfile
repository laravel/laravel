FROM php:8.2

WORKDIR /app

# Install system dependencies
RUN set -xe && \
    apt-get update && \
    apt-get install -y $PHPIZE_DEPS libpq-dev libsqlite3-dev sqlite3 git zip && \
    docker-php-ext-configure pdo_pgsql && \
    docker-php-ext-install pdo_pgsql ;

RUN set -xe && \
    apt-get remove -y $PHPIZE_DEPS

COPY --from=composer:2.8.1 /usr/bin/composer /usr/local/bin/composer



