#!/bin/sh
set -e

cd /var/www/html

if [ -n "$DB_HOST" ]; then
  echo "Waiting for database at ${DB_HOST}:${DB_PORT:-3306}..."
  tries=0
  until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306}', getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" > /dev/null 2>&1; do
    tries=$((tries + 1))
    if [ "$tries" -ge 30 ]; then
      echo "Database not reachable after 30 attempts, continuing anyway."
      break
    fi
    sleep 2
  done
fi

php artisan config:clear
php artisan migrate --force
php artisan config:cache
php artisan view:cache

exec "$@"
