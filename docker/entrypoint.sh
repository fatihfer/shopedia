#!/bin/sh
set -e

# Tunggu database siap (maks ~30 detik) bila DB_HOST terisi
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ] && [ "$DB_HOST" != "localhost" ]; then
    echo "Menunggu database $DB_HOST:$DB_PORT ..."
    for i in $(seq 1 30); do
        if php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT') ?: '3306'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Exception \$e) { exit(1); }"; then
            break
        fi
        sleep 1
    done
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Menjalankan migrasi ..."
    php artisan migrate --force
fi

exec "$@"
