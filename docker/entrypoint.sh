#!/bin/sh
set -e

cd /var/www/html

link_railway_volume() {
    volume_mount="${RAILWAY_VOLUME_MOUNT_PATH:-}"
    if [ -z "$volume_mount" ] || [ ! -d "$volume_mount" ]; then
        return 0
    fi

    echo "Linking Laravel storage to Railway volume at ${volume_mount}"

    persistent="${volume_mount}/laravel"
    mkdir -p \
        "${persistent}/app/private" \
        "${persistent}/app/public" \
        "${persistent}/framework/cache/laravel-excel" \
        "${persistent}/logs"

    for mapping in \
        "app:${persistent}/app" \
        "framework/cache/laravel-excel:${persistent}/framework/cache/laravel-excel" \
        "logs:${persistent}/logs"
    do
        rel_path="${mapping%%:*}"
        target="${mapping#*:}"
        storage_path="storage/${rel_path}"

        if [ -d "$storage_path" ] && [ ! -L "$storage_path" ]; then
            if [ -n "$(ls -A "$storage_path" 2>/dev/null)" ]; then
                cp -a "$storage_path/." "$target/" 2>/dev/null || true
            fi
            rm -rf "$storage_path"
        fi

        mkdir -p "$(dirname "$storage_path")"
        ln -sfn "$target" "$storage_path"
    done

    if [ ! -e public/storage ]; then
        php artisan storage:link --no-interaction 2>/dev/null || true
    fi
}

fix_permissions() {
    volume_mount="${RAILWAY_VOLUME_MOUNT_PATH:-}"
    if [ -n "$volume_mount" ] && [ -d "${volume_mount}/laravel" ]; then
        chown -R www-data:www-data "${volume_mount}/laravel"
    fi

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

link_railway_volume
fix_permissions
optimize_app

case "${1:-web}" in
    web)
        exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
        ;;
    queue)
        export HOME=/var/www
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
