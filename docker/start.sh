#!/bin/bash
set -e

# Railway injects $PORT — default to 80 if not set
PORT="${PORT:-80}"

echo "🚀 Starting ArcanePay on port $PORT"

# Update Apache to listen on Railway's dynamic PORT
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY not set, generating..."
    php artisan key:generate --force
fi

# Cache config, routes, views
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "📦 Running migrations..."
php artisan migrate --force

echo "✅ Boot complete. Starting Apache..."
exec apache2-foreground
