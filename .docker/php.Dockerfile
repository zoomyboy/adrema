FROM composer:2.7.9 AS composer
WORKDIR /app
COPY . /app
RUN composer install --ignore-platform-reqs --no-dev

FROM node:20.15.0-slim AS node
WORKDIR /app
COPY . /app
RUN cd packages/adrema-form && npm ci && npm run build && rm -R node_modules && cd ../../
RUN npm ci && npm run prod && npm run img && rm -R node_modules

FROM zoomyboy/adrema-base:latest AS php
COPY --chown=www-data:www-data . /app
COPY --chown=www-data:www-data --from=node /app/public /app/public
COPY --chown=www-data:www-data --from=composer /app/vendor /app/vendor

USER www-data
RUN php artisan telescope:publish
RUN php artisan horizon:publish

USER root
COPY ./.docker/php /bin

VOLUME ["/app/packages/laravel-nami/.cookies", "/app/storage/app", "/app/resources/views/tex/invoice"]

EXPOSE 9000

CMD /bin/php-entrypoint
