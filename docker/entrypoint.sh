#!/bin/bash
set -e

if [ ! -f /var/www/html/storage/framework/ready ]; then
    php artisan storage:link --force 2>/dev/null || true

    php artisan package:discover --ansi 2>/dev/null || true

    php artisan filament:assets --ansi 2>/dev/null || true

    if [ "$APP_ENV" != "local" ]; then
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
    fi

    php artisan migrate --force --seed

    touch /var/www/html/storage/framework/ready
fi

exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
