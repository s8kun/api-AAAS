FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY artisan ./

RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && composer install \
        --no-dev \
        --prefer-dist \
        --optimize-autoloader \
        --no-interaction

FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm ci && npm run build

FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_STACK=single \
    CACHE_STORE=file \
    SESSION_DRIVER=file \
    QUEUE_CONNECTION=sync \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/database/database.sqlite

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libsqlite3-dev \
    unzip \
    && docker-php-ext-install pdo_mysql pdo_pgsql pgsql pdo_sqlite \
    && a2enmod rewrite headers \
    && sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}/!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && rm -rf /var/lib/apt/lists/*

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY deploy/render/start.sh /usr/local/bin/start-render

RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache database \
    && chmod +x /usr/local/bin/start-render

EXPOSE 10000

CMD ["start-render"]
