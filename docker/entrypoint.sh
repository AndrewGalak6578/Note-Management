#!/bin/sh
set -e

cd /var/www/html

# 1) Create .env if missing
if [ ! -f .env ]; then
  cp .env.example .env
fi

# 2) Install PHP deps if missing (for fresh volume)
if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

# 3) Install JS deps if missing (for fresh volume)
if [ ! -d node_modules ]; then
  npm ci
fi

# 4) Build assets if missing (Vite manifest)
if [ ! -f public/build/manifest.json ]; then
  npm run build
fi

# 5) Generate APP_KEY if empty
php -r '
$env = file_get_contents(".env");
if (preg_match("/^APP_KEY=.*$/m", $env, $m)) {
  $val = trim(substr($m[0], strlen("APP_KEY=")));
  if ($val === "" || $val === "null") exit(1);
  exit(0);
}
exit(1);
' || php artisan key:generate --force

# 6) Clear cached config (на случай если кто-то кэшировал без ключа)
php artisan config:clear || true
php artisan cache:clear || true

# 7) Wait DB
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

# 8) Migrate
php artisan migrate --force

# 9) Permissions (минимально)
chown -R www-data:www-data storage bootstrap/cache || true

exec php-fpm
