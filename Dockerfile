FROM composer:2.2.7 as composer
WORKDIR /app
COPY . /app
RUN composer install --ignore-platform-reqs --no-dev

FROM node:17.9.0-slim as node
WORKDIR /app
COPY . /app
RUN npm install && npm run prod && rm -R node_modules

FROM php:8.1.6-fpm as php
RUN apt-get update && apt-get install -y libcurl3-dev apt-utils zlib1g-dev libpng-dev libicu-dev libonig-dev texlive
RUN docker-php-ext-install pdo_mysql curl gd intl mbstring
COPY --chown=www-data:www-data . /app
COPY --chown=www-data:www-data --from=node /app/public /app/public
COPY --chown=www-data:www-data --from=composer /app/vendor /app/vendor
EXPOSE 9000

CMD ["php-fpm", "-F", "-R", "-O"]
