FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_pgsql

# Instalar composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio app
WORKDIR /var/www

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Exponer puerto
EXPOSE 10000

# Iniciar Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000