FROM php:8.2-fpm-alpine

# Install Nginx, Supervisor, and the MySQL extension for PHP
RUN apk add --no-cache nginx supervisor \
    && docker-php-ext-install mysqli \
    && mkdir -p /run/nginx

# Copy configuration files from the current directory
COPY nginx.conf /etc/nginx/http.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy your interactive web application code
COPY index.php /var/www/html/index.php
WORKDIR /var/www/html

EXPOSE 80

# Start Supervisor to run Nginx and PHP-FPM simultaneously
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
