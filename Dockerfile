FROM php:8.2-fpm-bookworm AS base

RUN apt-get update && apt-get install -y \
    nginx \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libicu-dev \
    supervisor \
    curl \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        gd \
        zip \
        intl \
        bcmath \
        exif \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-scripts \
    && rm -rf /root/.composer/cache

COPY . .

RUN sed -i 's/fastcgi_pass app:9000/fastcgi_pass 127.0.0.1:9000/' docker/nginx/default.conf \
    && cp docker/nginx/default.conf /etc/nginx/sites-enabled/default \
    && mkdir -p /var/run/php \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && cp docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
