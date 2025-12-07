FROM php:8.1-apache

# PHP拡張
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Apacheのmod_rewriteを有効化
RUN a2enmod rewrite

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html