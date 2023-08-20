FROM php:8.1.6-fpm as php
WORKDIR /app
RUN apt-get update
RUN apt-get install -y rsync libcurl3-dev apt-utils zlib1g-dev libpng-dev libicu-dev libonig-dev unzip
RUN apt-get install -y --no-install-recommends texlive-base texlive-latex-base texlive-pictures texlive-latex-extra texlive-lang-german texlive-plain-generic texlive-fonts-recommended texlive-fonts-extra
RUN docker-php-ext-install pdo_mysql curl gd exif intl mbstring pcntl
RUN pecl install redis && docker-php-ext-enable redis
RUN usermod -s /bin/bash www-data
