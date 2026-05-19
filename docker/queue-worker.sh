#!/bin/sh
set -e

export HOME="${HOME:-/var/www}"
cd /var/www/html

exec php artisan queue:work \
    --sleep="${QUEUE_SLEEP:-3}" \
    --tries="${QUEUE_TRIES:-3}" \
    --max-time="${QUEUE_MAX_TIME:-3600}" \
    --timeout="${QUEUE_TIMEOUT:-120}"
