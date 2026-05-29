FROM php:8.4-fpm

# Extensions système
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql intl opcache zip mbstring \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier le code source (sans vendor)
COPY . .

# Installer les dépendances Composer (sans dev, optimisé)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Permissions cache/log Symfony
RUN mkdir -p var/cache var/log && \
    chown -R www-data:www-data var/ && \
    chmod -R 775 var/

# Config Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Config Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
