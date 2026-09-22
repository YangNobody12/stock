#!/bin/bash
set -e

# Configure Apache port based on Render's PORT environment variable
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Ensure storage and bootstrap/cache permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run database migrations and seeding
if [ -n "$DB_HOST" ] || [ -n "$DB_DATABASE" ]; then
    echo "Running database migrations and seeders..."
    php artisan migrate --force --seed || echo "Migration failed, check database connection."
fi

# Cache Laravel configuration, routes, and views if APP_KEY is provided
if [ -n "$APP_KEY" ]; then
    echo "Caching Laravel configuration, routes, and views..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "Starting Apache on port ${PORT}..."
exec apache2-foreground
