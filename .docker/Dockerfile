# Dockerfile for Laravel using serversideup/php

#
# Builder Stage (PHP Dependencies)
#
FROM serversideup/php:8.4-cli AS builder
USER root
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl
RUN install-php-extensions intl
WORKDIR /var/www/html
USER www-data
RUN git config --global --add safe.directory /var/www/html
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-scripts --no-plugins
COPY --chown=www-data:www-data . .
RUN composer run-script post-autoload-dump

#
# Node Builder Stage (Frontend Assets)
#
FROM node:20-alpine AS node-builder
WORKDIR /app
ARG VITE_APP_NAME
ARG VITE_PUSHER_APP_KEY
ARG VITE_PUSHER_HOST
ARG VITE_PUSHER_PORT
ARG VITE_PUSHER_SCHEME
ARG VITE_PUSHER_APP_CLUSTER
ENV VITE_APP_NAME=${VITE_APP_NAME}
ENV VITE_PUSHER_APP_KEY=${VITE_PUSHER_APP_KEY}
ENV VITE_PUSHER_HOST=${VITE_PUSHER_HOST}
ENV VITE_PUSHER_PORT=${VITE_PUSHER_PORT}
ENV VITE_PUSHER_SCHEME=${VITE_PUSHER_SCHEME}
ENV VITE_PUSHER_APP_CLUSTER=${VITE_PUSHER_APP_CLUSTER}
COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile
COPY . .
RUN yarn build

#
# ---- Final App Stage (Web Server) ----
# This is your final, lean image for the web application.

FROM serversideup/php:8.4-fpm-nginx AS api
WORKDIR /var/www/html
USER root
RUN install-php-extensions intl
COPY --from=builder /var/www/html /var/www/html

#Enable if you need node
#COPY --from=node-builder --chown=www-data:www-data /app/public/build /var/www/html/public/build

COPY ./.docker/49-laravel-api.sh /etc/entrypoint.d/49-laravel-api.sh
RUN chmod +x /etc/entrypoint.d/49-laravel-api.sh
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data

ENV AUTORUN_ENABLED=true
ENV PHP_OPCACHE_ENABLE=1 
ENV AUTORUN_LARAVEL_MIGRATION=true
ENV AUTORUN_LARAVEL_ROUTE_CACHE=true
ENV AUTORUN_LARAVEL_CONFIG_CACHE=true
ENV AUTORUN_LARAVEL_VIEW_CACHE=true
ENV AUTORUN_LARAVEL_EVENT_CACHE=true
ENV AUTORUN_LARAVEL_STORAGE_LINK=true
EXPOSE 8080

# ---- Final Scheduler Stage (CLI Worker) ----
# This is the new, optimized image just for your scheduler.
FROM serversideup/php:8.4-fpm-nginx AS scheduler
WORKDIR /var/www/html
USER root
# Install cron
RUN apt-get update && apt-get install -y cron
# Copy application code from the builder stage
COPY --from=builder /var/www/html /var/www/html
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
# Install any additional PHP extensions
RUN install-php-extensions intl
# Copy the scheduler entrypoint script
COPY ./.docker/49-laravel-scheduler.sh /etc/entrypoint.d/49-laravel-scheduler.sh
RUN chmod +x /etc/entrypoint.d/49-laravel-scheduler.sh
# Create the log directory and file to be able to run tail
RUN mkdir -p /var/www/html/storage/logs
RUN touch /var/www/html/storage/logs/cron.log
RUN chown -R www-data:www-data /var/www/html/storage/logs
ENV PHP_OPCACHE_ENABLE=1 
USER www-data

# ---- Final Queue Stage (CLI Worker) ----
# This is the new, optimized image just for your queue.
FROM serversideup/php:8.4-fpm-nginx AS queue
WORKDIR /var/www/html
USER root
# 3. Copy application code and cron files, still as ROOT.
COPY --from=builder /var/www/html /var/www/html
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
# 2. Install any additional PHP extensions needed for the queue.
RUN install-php-extensions intl
# Copy the queue entrypoint script
COPY ./.docker/49-laravel-queue.sh /etc/entrypoint.d/49-laravel-queue.sh
RUN chmod +x /etc/entrypoint.d/49-laravel-queue.sh
# 3. NOW, switch to the low-privilege user for running the application. ⬇️
USER www-data
ENV PHP_OPCACHE_ENABLE=1
EXPOSE 8080

# ---- All-in-One Stage (API + Queue + Scheduler) ----
# This combines the API server with queue worker and scheduler
FROM serversideup/php:8.4-fpm-nginx AS all-in-one
WORKDIR /var/www/html
USER root
# Install PHP extensions
RUN install-php-extensions intl
# Copy application code from the builder stage
COPY --from=builder /var/www/html /var/www/html

# Copy built frontend assets from node-builder
#Enable if you need node
#COPY --from=node-builder --chown=www-data:www-data /app/public/build /var/www/html/public/build

# Copy Laravel initialization script
COPY ./.docker/49-laravel-api.sh /etc/entrypoint.d/49-laravel-api.sh
RUN chmod +x /etc/entrypoint.d/49-laravel-api.sh

# Copy S6 service definitions
COPY ./.docker/s6-services/laravel-queue /etc/s6-overlay/s6-rc.d/laravel-queue
COPY ./.docker/s6-services/laravel-scheduler /etc/s6-overlay/s6-rc.d/laravel-scheduler
COPY ./.docker/s6-services/user/contents.d /etc/s6-overlay/s6-rc.d/user/contents.d

# Make run scripts executable
RUN chmod +x /etc/s6-overlay/s6-rc.d/laravel-queue/run
RUN chmod +x /etc/s6-overlay/s6-rc.d/laravel-scheduler/run

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data

# Enable Laravel optimizations
ENV AUTORUN_ENABLED=true
ENV PHP_OPCACHE_ENABLE=1
ENV AUTORUN_LARAVEL_MIGRATION=true
ENV AUTORUN_LARAVEL_ROUTE_CACHE=true
ENV AUTORUN_LARAVEL_CONFIG_CACHE=true
ENV AUTORUN_LARAVEL_VIEW_CACHE=true
ENV AUTORUN_LARAVEL_EVENT_CACHE=true

# Expose port 8080 for the API service
EXPOSE 8080
ENV AUTORUN_LARAVEL_STORAGE_LINK=true
