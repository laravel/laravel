#!/bin/bash

# Clear Laravel caches and then optimize
php artisan optimize:clear
php artisan optimize

# Start the scheduler
exec php artisan schedule:work