#!/bin/sh
set -e

export HOME="${HOME:-/var/www}"
cd /var/www/html

# Coerce to integers so pcntl_alarm() never receives malformed values from the environment.
QUEUE_SLEEP=$((${QUEUE_SLEEP:-3} + 0))
QUEUE_TRIES=$((${QUEUE_TRIES:-3} + 0))
QUEUE_MAX_TIME=$((${QUEUE_MAX_TIME:-3600} + 0))
QUEUE_TIMEOUT=$((${QUEUE_TIMEOUT:-120} + 0))

exec php artisan queue:work \
    --sleep="$QUEUE_SLEEP" \
    --tries="$QUEUE_TRIES" \
    --max-time="$QUEUE_MAX_TIME" \
    --timeout="$QUEUE_TIMEOUT"
