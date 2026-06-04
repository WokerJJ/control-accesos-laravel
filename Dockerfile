# ═══════════════════════════════════════════════════════════════
# Stage 1: Build frontend assets with Node.js
# ═══════════════════════════════════════════════════════════════
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci --ignore-scripts

COPY vite.config.js ./
COPY resources/ resources/

RUN npm run build

# ═══════════════════════════════════════════════════════════════
# Stage 2: Install PHP dependencies with Composer
# ═══════════════════════════════════════════════════════════════
FROM composer:2 AS composer

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
# Stage 3: Final image with PHP + Apache
# ═══════════════════════════════════════════════════════════════
FROM php:8.3-apache-bookworm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libcurl4-openssl-dev \
        libssl-dev \
        unzip \
        git \
        sqlite3 \
        libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        xml \
        zip \
        bcmath \
        curl \
        gd \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure PHP for production
RUN { \
        echo 'memory_limit=256M'; \
        echo 'upload_max_filesize=20M'; \
        echo 'post_max_size=25M'; \
        echo 'max_execution_time=60'; \
        echo 'max_input_time=60'; \
        echo 'date.timezone=America/Bogota'; \
    } > /usr/local/etc/php/conf.d/app.ini

# Configure OPcache for production
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.revalidate_freq=0'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Set working directory
WORKDIR /var/www/html

# Copy the application files first
COPY . .

# Copy vendor from composer stage (overrides local if present)
COPY --from=composer /app/vendor/ vendor/

# Copy built frontend assets from node stage
COPY --from=frontend /app/public/build/ public/build/
COPY --from=frontend /app/node_modules/ node_modules/

# Copy public AdminLTE assets (if not in node_modules build)
RUN if [ -d "node_modules/admin-lte/dist" ]; then \
        mkdir -p public/adminlte/dist && \
        cp -r node_modules/admin-lte/dist/* public/adminlte/dist/ ; \
    fi

# Create storage directories and set permissions
RUN mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
        public/storage \
    && chown -R www-data:www-data \
        storage \
        bootstrap/cache \
        public/storage \
    && chmod -R 775 \
        storage \
        bootstrap/cache

# Create .env if it doesn't exist
RUN if [ ! -f .env ]; then \
        cp .env.example .env ; \
    fi

# Create SQLite database file
RUN touch database/database.sqlite \
    && chown www-data:www-data database/database.sqlite

# Create the entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
