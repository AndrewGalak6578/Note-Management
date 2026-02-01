FROM php:8.3-fpm

# System packages + PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip curl \
    libzip-dev libonig-dev \
    libxml2-dev \
    libicu-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo pdo_mysql zip \
    xml intl gd \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get update && apt-get install -y nodejs \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# 1) СНАЧАЛА копируем весь проект (включая artisan)
COPY . .

# 2) PHP deps
RUN composer install --no-interaction --prefer-dist

# 3) JS deps + build
RUN npm ci && npm run build

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
