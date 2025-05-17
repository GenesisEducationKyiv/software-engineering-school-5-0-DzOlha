FROM nginx:stable-alpine3.20

WORKDIR /etc/nginx/conf.d

COPY ./infra/prod/nginx/default.conf .

COPY ./infra/prod/nginx/nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www/html

COPY ./src/public/ ./public

EXPOSE 80

# Runs nginx in the foreground
CMD ["nginx", "-g", "daemon off;"]