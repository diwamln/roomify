# --- STEP 1: Build Frontend (Vue/Inertia) ---
FROM node:20 as frontend
WORKDIR /app

# 1. Ambil package.json dari dalam folder src
COPY src/package*.json ./

# 2. Install dependencies node
RUN npm install

# 3. Copy seluruh isi folder src ke dalam container build
COPY src/ . 

# 4. Build aset
RUN npm run build

# --- STEP 2: Setup Production Server ---
FROM php:8.2-fpm

# Install Nginx & Supervisor
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libpq-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP Ext
RUN docker-php-ext-install pdo pdo_pgsql opcache

# Install Redis (Opsional)
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- PERBAIKAN BAGIAN CONFIG ---
# Asumsinya file nginx-deploy.conf & supervisord.conf ada di ROOT (bukan di dalam src)
RUN rm /etc/nginx/sites-enabled/default
COPY nginx-deploy.conf /etc/nginx/sites-enabled/default
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Setup Working Directory
WORKDIR /var/www/html

# --- PERBAIKAN KRUSIAL DISINI ---
# JANGAN 'COPY . .' (nanti folder src ikut masuk)
# TAPI 'COPY src/ .' (ambil ISI folder src, taruh di root www)
COPY src/ .

# Copy hasil build frontend
COPY --from=frontend /app/public/build public/build

# Install Laravel Dependencies
# (Sekarang aman karena composer.json sudah tercopy ke /var/www/html berkat perintah di atas)
RUN composer install --no-dev --optimize-autoloader

# Permission
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]