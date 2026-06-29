FROM php:8.2-fpm-alpine

# Install Nginx, Supervisor, and PHP MySQL extensions
RUN apk add --no-cache nginx supervisor \
    && docker-php-ext-install mysqli \
    && mkdir -p /run/nginx

# Copy configurations from the root folder
COPY nginx.conf /etc/nginx/http.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application file
COPY index.php /var/www/html/index.php
WORKDIR /var/www/html

EXPOSE 80

# Start Supervisor to run both services together
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
