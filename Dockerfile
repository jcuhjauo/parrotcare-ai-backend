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
    libpq-dev \
    oniguruma-dev \
    libzip-dev \
    postgresql-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip bcmath

# 安裝 Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 先複製 composer 檔案,加速 docker layer cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 複製其餘程式碼
COPY . .

RUN composer dump-autoload --optimize \
    && php artisan config:cache \
    && php artisan route:cache

# 權限設定
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# nginx 設定檔
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]