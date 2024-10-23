# Use an official PHP runtime as a parent image
FROM php:8.1

# Set the working directory to /app
WORKDIR /app

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        unzip \
        libonig-dev \
        libxml2-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev && \
    docker-php-ext-install pdo_mysql zip mbstring exif pcntl bcmath gd && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer self-update --2


# Copy the Laravel application files to the container
COPY . .

# Install application dependencies
RUN echo "which composer"  # Check if Composer is in PATH
RUN composer install --no-interaction --optimize-autoloader


# Set application key
RUN php -r "file_exists('.env') || copy('.env.example', '.env');"
RUN php artisan key:generate --force

# Set permissions for Laravel storage and bootstrap cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 9000 and start PHP-FPM server
EXPOSE 9000
CMD ["php-fpm"]