# syntax=docker/dockerfile:1.7
ARG COMPOSER_VERSION="2.2"
ARG PHP_VERSION=7.0

FROM composer/composer:${COMPOSER_VERSION} AS composer

FROM php:${PHP_VERSION}-cli AS php
ARG PHP_EXTENSIONS="zip mcrypt libsodium"
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions
RUN install-php-extensions ${PHP_EXTENSIONS}
RUN apt update && apt -y install git && rm -rf /var/lib/apt/lists/*
COPY --from=composer /usr/bin/composer /usr/bin/composer

FROM php as php_with_xdebug
ARG XDEBUG_VERSION=2.5.5
RUN pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug \
    && rm -rf /var/cache/apt/archives /var/lib/apt/lists
ADD docker/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
