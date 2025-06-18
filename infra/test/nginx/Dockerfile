FROM nginx:stable-alpine3.20

WORKDIR /etc/nginx/conf.d

COPY ./infra/test/nginx/default.conf .

WORKDIR /var/www/html

COPY ./src/public/ ./public
COPY ./src/resources ./resources

EXPOSE 80

# Runs nginx in the foreground
CMD ["nginx", "-g", "daemon off;"]