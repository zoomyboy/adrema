FROM composer:2.2.7 as composer
WORKDIR /app
COPY . /app
RUN composer install --ignore-platform-reqs --no-dev
RUN php artisan telescope:publish
RUN php artisan horizon:publish

FROM node:17.9.0-slim as node
WORKDIR /app
COPY . /app
RUN npm install && npm run prod && npm run img && rm -R node_modules

FROM nginx:1.21.6-alpine as nginx
WORKDIR /app
COPY --from=node /app /app
COPY --from=composer /app/public/vendor /app/public/vendor
COPY ./.docker/nginx/nginx.conf /etc/nginx/nginx.conf
EXPOSE 80

VOLUME ["/app/public/storage"]

CMD ["nginx", "-g", "daemon off;"]
