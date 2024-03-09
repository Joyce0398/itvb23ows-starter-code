FROM composer:2.6.6 AS composer
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs


FROM php:8.2-fpm
WORKDIR /usr/src/hive

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install session && docker-php-ext-enable session

# XDEBUG
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY ./xdebug.ini "${PHP_INI_DIR}/conf.d"

COPY --from=composer /app/vendor /usr/src/hive/vendor

COPY . /usr/src/hive

EXPOSE 80
