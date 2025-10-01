#!/bin/sh
composer install  --optimize-autoloader
composer dump-autoload
php artisan migrate --force
php artisan optimize
php artisan app:config




log_files=(
    "/var/log/supervisord.log"
    "/var/log/nginx-error.log"
    "/var/log/nginx-access.log"
    "/var/log/php-access.log"
    "/var/log/php-error.log"
    "/var/log/schedule.log"
    "/var/log/notification.log"
    "/var/log/worker.log"
)


# Loop through the log file paths and create them with chmod 666
for log_file in "${log_files[@]}"; do
    touch "$log_file"
    chmod 666 "$log_file"
done

# Ensure Laravel log directory exists
mkdir -p /var/www/html/storage/logs

# Create laravel.log if it doesn't exist
if [ ! -f /var/www/html/storage/logs/laravel.log ]; then
    touch /var/www/html/storage/logs/laravel.log
    chown www-data:www-data /var/www/html/storage/logs/laravel.log
    chmod 664 /var/www/html/storage/logs/laravel.log
fi

# Symlink Laravel log into /var/log so it appears in ./logs/php on the host
if [ ! -L /var/log/laravel.log ]; then
    ln -s /var/www/html/storage/logs/laravel.log /var/log/laravel.log
fi

# Start supervisord in foreground
exec /usr/bin/supervisord -c /var/www/html/docker/supervisord.conf

# /usr/bin/supervisord -c ./docker/supervisord.conf
