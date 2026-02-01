#!/bin/sh
set -e

cd /var/www/html

# .env
if [ ! -f .env ]; then
  cp .env.example .env
fi

# vendor
if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

# node_modules
if [ ! -d node_modules ]; then
  npm ci
fi

# build manifest (vite)
if [ ! -f public/build/manifest.json ]; then
  npm run build
fi

# APP_KEY
php artisan key:generate --force || true

# ждём MySQL
php -r '
$host = getenv("DB_HOST") ?: "db";
$port = getenv("DB_PORT") ?: "3306";
$start = time();
while (time() - $start < 60) {
  $fp = @fsockopen($host, (int)$port, $errno, $errstr, 1);
  if ($fp) { fclose($fp); exit(0); }
  usleep(300000);
}
fwrite(STDERR, "DB not ready\n");
exit(1);
'

# миграции
php artisan migrate --force

exec php-fpm
