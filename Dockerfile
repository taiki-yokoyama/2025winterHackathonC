FROM php:8.1-apache

# 必要な拡張機能をインストール
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Apacheのmod_rewriteを有効化
RUN a2enmod rewrite

# 作業ディレクトリを設定
WORKDIR /var/www/html

# 権限設定
RUN chown -R www-data:www-data /var/www/html