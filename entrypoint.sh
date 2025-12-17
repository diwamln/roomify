#!/bin/sh
set -e

# Pesan log agar kita tahu script jalan
echo "🚀 Starting Roomify Entrypoint..."

# 1. Pastikan folder storage & cache ada
mkdir -p storage/framework/cache \
         storage/framework/sessions \
         storage/framework/testing \
         storage/framework/views \
         storage/logs \
         storage/app/public \
         bootstrap/cache

# 2. Fix Permissions
# Ubah owner ke www-data (user default php-fpm)
# Kita lakukan ini sebelum switch user (saat masih root)
echo "🔧 Fixing permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 3. Jalankan Cache Laravel (Opsional tapi bagus untuk Production)
# Jika DB belum ready, command ini mungkin error, jadi kita pakai '|| true' agar container tidak crash
echo "🔥 Caching configuration..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# 4. Jalankan Command Utama (biasanya php-fpm)
echo "✅ Executing command: $@"
exec "$@"
