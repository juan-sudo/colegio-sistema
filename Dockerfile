FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# INSTALAR DEPENDENCIAS DE PHP (CORREGIDO)
RUN composer install --optimize-autoloader --no-interaction

# Instalar dependencias de Node y compilar assets (Tailwind)
RUN npm install && npm run build

# Cachear configuración
RUN php artisan config:cache && php artisan route:cache

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto
EXPOSE 10000

# Comando de inicio
CMD php artisan serve --host=0.0.0.0 --port=10000