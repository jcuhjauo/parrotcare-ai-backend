#!/bin/bash
set -e

cd /var/www/html

# 確保 storage 連結存在(圖片可公開存取用,免費方案下重啟後本機檔案仍會消失,僅供過渡)
php artisan storage:link || true

# 每次部署自動跑 migration(正式環境建議之後改成手動執行,先求上線方便)
php artisan migrate --force || true

# 啟動 nginx + php-fpm
exec supervisord -c /etc/supervisord.conf