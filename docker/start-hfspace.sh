#!/bin/bash
set -e

echo "=== EventFlow HF Spaces Startup ==="

cd /var/www

# Generate key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Create storage directories
mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
chmod -R 777 storage bootstrap/cache

# Create storage symlink
rm -f public/storage
php artisan storage:link --force 2>/dev/null || true

# Wait for PostgreSQL to be ready
echo "Connecting to PostgreSQL..."
MAX_RETRIES=10
RETRY_COUNT=0
until php -r "
    \$host = getenv('DB_HOST') ?: 'localhost';
    \$port = getenv('DB_PORT') ?: '5432';
    \$dbname = getenv('DB_DATABASE') ?: 'eventflow';
    \$user = getenv('DB_USERNAME') ?: 'eventflow';
    \$pass = getenv('DB_PASSWORD') ?: '';
    \$sslmode = getenv('DB_SSLMODE') ?: 'require';
    try {
        new PDO(\"pgsql:host=\$host;port=\$port;dbname=\$dbname;sslmode=\$sslmode\", \$user, \$pass);
        echo 'Connected!';
        exit(0);
    } catch (Exception \$e) {
        echo \$e->getMessage();
        exit(1);
    }
" 2>/dev/null; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "WARNING: Could not connect to PostgreSQL after ${MAX_RETRIES} attempts."
        break
    fi
    echo "PostgreSQL not ready... (attempt $RETRY_COUNT/$MAX_RETRIES)"
    sleep 2
done

# Run migrations
echo "Running migrations..."
php artisan migrate --force 2>&1 || echo "Migration warning"

# Seed database
echo "Seeding database..."
php artisan db:seed --force 2>&1 || echo "Seed warning (data may exist)"

# Cache for performance
echo "Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configure Apache to listen on port 7860 (HF Spaces default)
PORT=${PORT:-7860}
echo "Configuring Apache on port $PORT..."
sed -i "s/Listen 80$/Listen 0.0.0.0:$PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf

# Fix ServerName warning
echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Guarantee Laravel routing
a2enmod rewrite headers -q || true
echo "<Directory /var/www/public>" >> /etc/apache2/apache2.conf
echo "    Options Indexes FollowSymLinks" >> /etc/apache2/apache2.conf
echo "    AllowOverride All" >> /etc/apache2/apache2.conf
echo "    Require all granted" >> /etc/apache2/apache2.conf
echo "</Directory>" >> /etc/apache2/apache2.conf

# Fix permissions
chmod -R 777 storage bootstrap/cache

# Tail Laravel logs
touch storage/logs/laravel.log
chmod 777 storage/logs/laravel.log
tail -n 0 -f storage/logs/laravel.log &

# Load Apache env
source /etc/apache2/envvars 2>/dev/null || true

# Fix MPM conflicts
rm -f /etc/apache2/mods-enabled/mpm_event.conf /etc/apache2/mods-enabled/mpm_event.load
rm -f /etc/apache2/mods-enabled/mpm_worker.conf /etc/apache2/mods-enabled/mpm_worker.load
a2enmod mpm_prefork -q || true

echo "=== EventFlow Ready on port $PORT ==="

# Start Apache in foreground
exec apache2-foreground
