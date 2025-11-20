# --- TAHAP 1: Install Vendor PHP (Composer) ---
FROM composer:2 AS vendor-build
WORKDIR /app

COPY src/composer.json src/composer.lock ./src/
RUN composer install \
    --working-dir=./src \
    --no-dev \
    --ignore-platform-reqs \
    --no-scripts \
    --prefer-dist


# --- TAHAP 2: Build Frontend (Vue/Inertia) ---
FROM node:20 AS frontend
WORKDIR /app

COPY src/ ./src/
COPY --from=vendor-build /app/src/vendor ./src/vendor

RUN npm install --prefix ./src
RUN npm run build --prefix ./src


# --- TAHAP 3: Setup Production Server (Final) ---
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libpq-dev \
    zip unzip git curl

RUN docker-php-ext-install pdo pdo_pgsql opcache
RUN pecl install redis && docker-php-ext-enable redis

# Nginx & Supervisor config
RUN rm /etc/nginx/sites-enabled/default
COPY nginx-deploy.conf /etc/nginx/sites-enabled/default
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf


# ==========================================
# FIX PENTING SAAT BUILD (biar tidak connect Redis/DB)
# ==========================================
ENV APP_ENV=production
ENV APP_DEBUG=false

ENV CACHE_DRIVER=array
ENV CACHE_STORE=array
ENV CACHE_DEFAULT=array

ENV SESSION_DRIVER=array
ENV QUEUE_CONNECTION=sync


WORKDIR /var/www/html

# Copy source code (termasuk .env kalau ada)
COPY src/ .
# Render will inject env vars → write them for Laravel
RUN printenv | grep -E 'APP_|DB_|CACHE_|SESSION_' > /var/www/html/.env || true


# HAPUS .env AGAR BUILD TIDAK TERGANGGU CONFIG NYATA
RUN rm -f .env

# Copy vendor & frontend build
COPY --from=vendor-build /app/src/vendor ./vendor
COPY --from=frontend /app/src/public/build ./public/build

# Clean old caches
RUN rm -f bootstrap/cache/packages.php \
    && rm -f bootstrap/cache/services.php

# Set permission
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache


# ==========================================
# BUILD ARTISAN CACHE (DIJAMIN TANPA ERROR)
# ==========================================
RUN php artisan optimize:clear


EXPOSE 80

# >>> PERUBAHAN SEMENTARA UNTUK SETUP DATABASE:
# Jalankan migrasi, storage link, dan seeder, lalu keluar (exit)
# >>> KEMBALIKAN KE PERINTAH WEB SERVER NORMAL
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
