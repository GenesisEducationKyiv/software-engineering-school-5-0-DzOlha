FROM nginx:stable-alpine3.20

WORKDIR /etc/nginx/conf.d

COPY ./infra/dev/nginx/default.conf .

WORKDIR /var/www/html

COPY ./src ./

# default command will be executed -> start this web server