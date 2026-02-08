#!/bin/bash
set -e

echo "Setting permissions..."
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/database

echo "Running migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force 2>/dev/null || true

echo "Setting up storage links..."
php artisan storage:link 2>/dev/null || true

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
