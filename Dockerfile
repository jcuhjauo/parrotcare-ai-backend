# ---- Base image ----
FROM php:8.4-fpm-alpine

# 安裝系統套件與 PHP 需要的擴充套件
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    git \
    unzip \
    libzip-dev \
    postgresql-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip bcmath \
    && mkdir -p /run/nginx

# 安裝 Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 先複製 composer 檔案，加速 docker layer cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 複製其餘程式碼
COPY . .

RUN composer dump-autoload --optimize

# 權限設定
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# PHP 設定：放寬上傳大小與執行時間（病歷照片、Gemini OCR）
RUN printf "upload_max_filesize=20M\npost_max_size=20M\nmax_execution_time=120\n" > /usr/local/etc/php/conf.d/app.ini

# nginx / supervisor / 啟動腳本
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh
# 修正 Windows 換行（CRLF -> LF），避免 "no such file or directory"
RUN sed -i 's/\r$//' /start.sh && chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]