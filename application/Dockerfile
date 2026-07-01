FROM php:8.2-fpm-alpine

# Install build dependencies, Nginx, Supervisor, and BOTH extensions
RUN apk add --no-cache nginx supervisor mysql-client \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && mkdir -p /run/nginx

COPY nginx.conf /etc/nginx/http.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY index.php /var/www/html/index.php

WORKDIR /var/www/html
EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
