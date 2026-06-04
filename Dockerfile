# ═══════════════════════════════════════════════════════════════
# Stage 1: Build frontend assets
# ═══════════════════════════════════════════════════════════════
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci --ignore-scripts
COPY vite.config.js ./
COPY resources/ resources/
RUN npm run build

# ═══════════════════════════════════════════════════════════════
# Stage 2: Install PHP dependencies
# ═══════════════════════════════════════════════════════════════
FROM composer:2 AS deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ═══════════════════════════════════════════════════════════════
# Stage 3: Final image — PHP 8.3 + Apache
# ═══════════════════════════════════════════════════════════════
FROM php:8.3-apache-bookworm

# System deps + PHP extensions (MySQL only)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libonig-dev libxml2-dev libzip-dev libcurl4-openssl-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip bcmath gd opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP config
RUN { \
    echo 'memory_limit=256M'; \
    echo 'upload_max_filesize=20M'; \
    echo 'post_max_size=25M'; \
    echo 'max_execution_time=60'; \
    echo 'date.timezone=America/Bogota'; \
} > /usr/local/etc/php/conf.d/app.ini

RUN a2enmod rewrite

WORKDIR /var/www/html

# 1) Copy source code
COPY . .

# 2) Copy vendor from Composer stage
COPY --from=deps /app/vendor/ vendor/

# 3) Copy built assets from Node stage
COPY --from=frontend /app/public/build/ public/build/

# 4) Create dirs and .env
RUN mkdir -p storage/app/public storage/framework/{cache/data,sessions,views} \
        storage/logs bootstrap/cache public/storage \
    && chown -R www-data:www-data storage bootstrap/cache public/storage \
    && chmod -R 775 storage bootstrap/cache

# 5) Copy entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
