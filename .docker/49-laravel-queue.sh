#!/bin/bash

# Clear Laravel caches and then optimize
php artisan optimize:clear
php artisan optimize

# Start the queue worker
exec php artisan queue:work --tries=3