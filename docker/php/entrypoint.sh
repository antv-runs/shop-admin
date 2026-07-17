#!/bin/sh
set -e

cd /var/www

# Luôn chạy composer install để đảm bảo vendor được cập nhật
composer install --no-interaction --prefer-dist

mkdir -p storage/logs \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

touch storage/logs/laravel.log storage/logs/query.log
chmod 666 storage/logs/laravel.log storage/logs/query.log
chmod -R 777 storage bootstrap/cache

# Tạo symlink cho storage nếu chưa có (để public file ảnh, upload,...)
if [ ! -L "public/storage" ]; then
    php artisan storage:link
fi

exec "$@"
