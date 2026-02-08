#!/bin/bash
set -e

echo "Setting up storage links..."
php artisan storage:link 2>/dev/null || true

echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Running migrations..."
php artisan migrate --force --seed 2>/dev/null || php artisan migrate --force

echo "Setting permissions..."
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/database

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
