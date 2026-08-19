#!/bin/bash
set -e

cd /var/www/html

# 清掉 build 階段留下的舊快取(可能包含錯誤的空值設定)
php artisan config:clear
php artisan route:clear

# 用「現在真正拿到的」環境變數重新建立快取
php artisan config:cache
php artisan route:cache

# 上面幾個指令用 root 執行,可能動到檔案權限,這裡修正回來
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 確保 storage 連結存在(圖片可公開存取用,免費方案下重啟後本機檔案仍會消失,僅供過渡)
php artisan storage:link || true

# 每次部署自動跑 migration(正式環境建議之後改成手動執行,先求上線方便)
php artisan migrate --force || true

# 啟動 nginx + php-fpm
exec supervisord -c /etc/supervisord.conf