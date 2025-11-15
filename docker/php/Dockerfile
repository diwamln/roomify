# Base image PHP
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    curl \
    gnupg \
    lsb-release \
    sudo \
    build-essential \
    zip \
    libzip-dev \
    vim \
    nano

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql zip

# Install Redis PHP extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js + npm (LTS v20)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Verify installations
RUN php -v && composer -V && node -v && npm -v

# Copy existing application (optional)
# COPY . .

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
