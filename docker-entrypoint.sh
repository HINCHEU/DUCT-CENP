#!/bin/bash
set -e

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear and cache configurations
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache in the foreground
echo "Starting Apache..."
apache2-foreground
