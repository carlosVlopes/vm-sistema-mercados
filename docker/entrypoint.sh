#!/bin/bash
set -e

chmod -R 775 storage bootstrap/cache

php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan migrate:fresh --force

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
