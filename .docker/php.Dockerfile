FROM composer:2.2.7 as composer
WORKDIR /app
COPY . /app
RUN composer install --ignore-platform-reqs --no-dev

FROM node:17.9.0-slim as node
WORKDIR /app
COPY . /app
RUN npm install && npm run prod && npm run img && rm -R node_modules

FROM php:8.1.6-fpm as php
WORKDIR /app
RUN apt-get update
RUN apt-get install -y rsync libcurl3-dev apt-utils zlib1g-dev libpng-dev libicu-dev libonig-dev unzip
RUN apt-get install -y --no-install-recommends texlive-base texlive-latex-base texlive-pictures texlive-latex-extra texlive-lang-german texlive-plain-generic texlive-fonts-recommended texlive-fonts-extra
RUN docker-php-ext-install pdo_mysql curl gd intl mbstring pcntl
RUN pecl install redis && docker-php-ext-enable redis
COPY --chown=www-data:www-data . /app
COPY --chown=www-data:www-data --from=node /app/public /app/public
COPY --chown=www-data:www-data --from=composer /app/vendor /app/vendor
EXPOSE 9000

CMD ["php-fpm", "-F", "-R", "-O"]
