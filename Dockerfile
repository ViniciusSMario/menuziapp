# Etapa de build do frontend
FROM node:18-alpine as nodebuilder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build || npm run dev

# Etapa principal do PHP
FROM php:8.2-fpm

# Dependências
RUN apt-get update && apt-get install -y \
    git curl zip unzip netcat-openbsd \
    libpng-dev libonig-dev libxml2-dev libzip-dev libjpeg-dev libfreetype6-dev \
    libxslt-dev libcurl4-openssl-dev libicu-dev libgmp-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd intl bcmath opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# OPCache
COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Código da aplicação
WORKDIR /var/www
COPY . .

# Copia frontend compilado
COPY --from=nodebuilder /app/public/js ./public/js
COPY --from=nodebuilder /app/public/css ./public/css

# Permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]