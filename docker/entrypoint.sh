#!/bin/sh
set -e
cd /var/www/html

# The storage volume starts empty on first run; recreate Laravel's folders.
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions \
         storage/framework/views storage/logs bootstrap/cache

# Generate an APP_KEY once and keep it in the storage volume if none was given.
if [ -z "$APP_KEY" ]; then
    if [ ! -f storage/app/.app_key ]; then
        echo "base64:$(head -c 32 /dev/urandom | base64)" > storage/app/.app_key
    fi
    export APP_KEY="$(cat storage/app/.app_key)"
fi

chown -R www-data:www-data storage bootstrap/cache

echo "Waiting for database at ${DB_HOST}:${DB_PORT:-3306}..."
until mariadb-admin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USERNAME" -p"$DB_PASSWORD" --skip-ssl --silent; do
    sleep 2
done

php artisan migrate --force

# Seed demo data only on the very first start (empty users table).
if [ "${SEED_ON_FIRST_RUN:-true}" = "true" ]; then
    USERS=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -n1)
    if [ "$USERS" = "0" ]; then
        echo "Empty database, seeding demo data..."
        php artisan db:seed --force
    fi
fi

[ -L public/storage ] || php artisan storage:link
php artisan config:cache
php artisan view:cache

exec "$@"
