FROM node:17.9.0-slim as node
WORKDIR /app
COPY . /app
RUN npm install && npm run prod && npm run img && rm -R node_modules

FROM nginx:1.21.6-alpine as nginx
WORKDIR /app
COPY --from=node /app /app
COPY ./.docker/nginx/nginx.conf /etc/nginx/nginx.conf
EXPOSE 80

VOLUME ["/app/storage/app"]

CMD ["nginx", "-g", "daemon off;"]
