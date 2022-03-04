FROM php:8.1.3-fpm-buster

WORKDIR /app

RUN useradd -d /app -s /bin/bash runner

RUN sed -i 's/user = www-data/user = runner/' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/group = www-data/group = runner/' /usr/local/etc/php-fpm.d/www.conf

RUN apt-get clean && apt-get update && apt-get install -y apt-utils
RUN apt-get install -y libsodium-dev curl libpng-dev git zip


RUN docker-php-ext-install mysqli pdo pdo_mysql sodium bcmath exif gd pcntl

RUN echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm composer-setup.php

