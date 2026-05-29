FROM php:8.4-fpm

# Extensions système + openssl pour les clés JWT
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    openssl \
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

# Copier le code source
COPY . .

# Installer les dépendances Composer (sans dev, optimisé)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Générer les clés JWT RSA si elles n'existent pas (clés gitignorées)
RUN mkdir -p config/jwt && \
    if [ ! -f config/jwt/private.pem ]; then \
        openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096 2>/dev/null && \
        openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem 2>/dev/null; \
    fi && \
    chmod 644 config/jwt/private.pem config/jwt/public.pem

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
