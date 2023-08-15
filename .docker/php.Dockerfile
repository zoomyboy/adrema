FROM composer:2.2.7 as composer
WORKDIR /app
COPY . /app
RUN composer install --ignore-platform-reqs --no-dev

FROM node:17.9.0-slim as node
WORKDIR /app
COPY . /app
RUN npm install && npm run prod && npm run img && rm -R node_modules

FROM zoomyboy/adrema-base:latest as php
COPY --chown=www-data:www-data . /app
COPY --chown=www-data:www-data --from=node /app/public /app/public
COPY --chown=www-data:www-data --from=composer /app/vendor /app/vendor

USER www-data
RUN php artisan telescope:publish
RUN php artisan horizon:publish

USER root
COPY ./.docker/php /bin

VOLUME ["/app/packages/laravel-nami/.cookies", "/app/storage/app"]

EXPOSE 9000

CMD /bin/php-entrypoint
