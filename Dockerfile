# Build stage - compile frontend
FROM node:20-alpine AS frontend-build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Production stage - Nginx + PHP-FPM
FROM php:8.2-fpm-alpine

# Install nginx and required PHP extensions
RUN apk add --no-cache nginx curl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy frontend build
COPY --from=frontend-build /app/dist /var/www/html

# Copy PHP backend
COPY php-backend /var/www/api
COPY agents.json /var/www/agents.json
WORKDIR /var/www/api

# Install PHP dependencies (skip if vendor already exists)
RUN if [ ! -d "vendor" ]; then composer install --no-dev --optimize-autoloader; fi

# Fix permissions for PHP-FPM (runs as nobody:nobody in Alpine)
RUN chown -R nobody:nobody /var/www && \
    chmod -R 755 /var/www && \
    chmod -R 775 /var/www/api/storage

# Create startup script
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'php-fpm -D' >> /start.sh && \
    echo 'nginx -g "daemon off;"' >> /start.sh && \
    chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
