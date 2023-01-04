FROM php:8.1-fpm

ARG ROOT=/app
ARG APP_ENV
ARG APP_PORT

WORKDIR $ROOT

ENV APP_ENV $APP_ENV
ENV PORT $APP_PORT

# Install and Update Libraries
RUN apt-get update && apt-get install -y autoconf gcc bash g++ make wget unzip libaio1 libaio-dev libxml2 libxml2-dev iputils-ping gettext-base nginx supervisor vim nano gnupg2

# Dockerize Command
ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

# Install  Sockets
RUN docker-php-ext-install sockets

# Install Postgres
RUN apt-get install -y libpng-dev libpq-dev libzip-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo pdo_pgsql \
    && docker-php-ext-install gd pcntl zip

# Install Heroku
RUN curl https://cli-assets.heroku.com/install.sh | sh
RUN apt-get update && apt-get install -y jq && apt-get clean

# Install Redis
RUN pecl install -o -f redis \
    && docker-php-ext-enable redis

# Install SOAP
RUN docker-php-ext-install soap

# Copy Composer Instalation
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy config files to container
COPY /devops/supervisor/supervisor.conf /etc/supervisord.conf
COPY /devops/nginx/default.conf.template /etc/nginx/conf.d/default.conf.template
COPY /devops/nginx/nginx.conf /etc/nginx/nginx.conf
COPY /devops/php-fpm/php.ini /usr/local/etc/php/conf.d/app.ini
COPY /devops/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY /devops/php-fpm/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini

COPY .env.example ${ROOT}/.env

COPY ./ $ROOT

RUN if [ "$APP_ENV" = "local" ]; then \
        composer install --prefer-dist --optimize-autoloader; \
    else \
        composer install --prefer-dist --optimize-autoloader --no-dev; \
    fi

ADD /devops/scripts/entrypoint.sh /
RUN chmod +x /entrypoint.sh

CMD ["/bin/bash", "-c", "/entrypoint.sh ${PORT}"]
