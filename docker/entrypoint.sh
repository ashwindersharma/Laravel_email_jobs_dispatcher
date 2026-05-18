#!/bin/sh
set -e

cd /var/www/html

fix_permissions() {
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R ug+rwx storage bootstrap/cache
}

optimize_app() {
    if [ -z "$APP_KEY" ]; then
        echo "WARNING: APP_KEY is not set. Set it before deploying to production."
        return 0
    fi

    php artisan config:clear
    php artisan view:clear
    php artisan package:discover --ansi
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
        php artisan migrate --force --no-interaction
    fi
}

fix_permissions
optimize_app

case "${1:-web}" in
    web)
        exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
        ;;
    queue)
        exec php artisan queue:work \
            --sleep="${QUEUE_SLEEP:-3}" \
            --tries="${QUEUE_TRIES:-3}" \
            --max-time="${QUEUE_MAX_TIME:-3600}" \
            --timeout="${QUEUE_TIMEOUT:-120}"
        ;;
    scheduler)
        exec php artisan schedule:work
        ;;
    *)
        exec "$@"
        ;;
esac
