FROM php:8.1-fpm-alpine

# 安裝系統依賴
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    git \
    zip \
    unzip \
    curl \
    oniguruma-dev \
    sqlite-dev \
    libzip-dev

# 安裝 PHP 擴充套件
RUN docker-php-ext-install pdo_mysql mbstring gd exif pcntl

# 安裝 Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 設定工作目錄
WORKDIR /var/www/html

# 清理
RUN rm -rf /var/cache/apk/*

EXPOSE 9000
CMD ["php-fpm"]
