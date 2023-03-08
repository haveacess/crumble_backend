FROM php:8.1-fpm-alpine3.15

ENV COMPOSER_ALLOW_SUPERUSER=1

#install composer locally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#enable extensions for working with db
RUN docker-php-ext-install mysqli pdo pdo_mysql  \
    && docker-php-ext-enable pdo_mysql