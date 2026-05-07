# 1 Base: PHP 8.4
FROM php:8.4-cli

# 2 Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && apt-get clean

# 3 Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4 Directorio de trabajo
WORKDIR /var/www

# 5 Copiar proyecto
COPY . .

# 6 Evitar problemas de memoria con Composer
ENV COMPOSER_MEMORY_LIMIT=-1

# 7 Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# 8 Dar permisos a storage y bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# 9 Exponer puerto para Render
EXPOSE 10000

# Comando para iniciar Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000