# --- STAGE 1: Build Dependencies (Composer) ---
FROM composer:2 AS deps

WORKDIR /app

# Copy hanya file composer dulu untuk memanfaatkan caching layer Docker
COPY src/composer.json src/composer.lock ./

# Install dependensi (tanpa dev tools, optimize classmap)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts --prefer-dist

# --- STAGE 2: Runtime (PHP-FPM) ---
FROM php:8.4-fpm-alpine

# Install library sistem yang dibutuhkan
# libpq-dev: untuk driver postgresql
# libzip-dev: untuk ekstensi zip
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    bash

# Install Ekstensi PHP
# pdo_pgsql: wajib untuk Laravel + Postgres
# opcache: wajib untuk performa production
# pcntl: berguna jika nanti pakai queue worker
RUN docker-php-ext-install pdo pdo_pgsql zip opcache pcntl

# Konfigurasi PHP-FPM agar listen di semua interface (penting untuk Kubernetes)
RUN sed -i 's|listen = 127.0.0.1:9000|listen = 9000|' /usr/local/etc/php-fpm.d/www.conf

# Setup working directory
WORKDIR /var/www/html

# Copy folder vendor dari stage 1
COPY --from=deps /app/vendor ./vendor

# Copy seluruh source code aplikasi
COPY src .

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Pastikan entrypoint bisa dieksekusi (antisipasi jika permission dari git salah)
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 9000 (default PHP-FPM)
EXPOSE 9000

# Set entrypoint dan command default
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
