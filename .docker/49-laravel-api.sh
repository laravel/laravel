#!/bin/sh
# Exit immediately if a command exits with a non-zero status.
set -e

# Change to the application directory
cd /var/www/html

# Check if it's a Laravel project by looking for the artisan file
if [ -f "artisan" ]; then
    echo "Running php artisan optimize:clear..."
    php artisan optimize:clear
    php artisan optimize
fi