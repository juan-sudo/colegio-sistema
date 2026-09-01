FROM php:8.2-fpm

# Instalar dependencias del sistema (AGREGADO: libzip-dev)
RUN apt-get update && apt-get install -y git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip nodejs npm libjpeg62-turbo-dev libfreetype6-dev libwebp-dev

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Crear carpetas necesarias para Laravel (excluidas del build por .dockerignore)
RUN mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public
RUN chmod -R 775 bootstrap/cache storage
RUN chown -R www-data:www-data bootstrap/cache storage
RUN chmod 775 database
RUN chown www-data:www-data database

# Instalar dependencias de PHP
RUN composer install --optimize-autoloader --no-interaction --ignore-platform-req=ext-zip

# Instalar dependencias de Node y compilar assets (Tailwind)
RUN npm install && npm run build

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto
EXPOSE 10000

# Nota: config:cache y route:cache se ejecutan al iniciar el contenedor (no en build),
# porque las variables de entorno reales (APP_KEY, DB_*, etc.) solo estan disponibles
# en tiempo de ejecucion en Render, no durante el build de la imagen.
CMD php artisan config:clear && php artisan route:clear && touch database/database.sqlite && chown www-data:www-data database/database.sqlite && php artisan migrate --force && (php artisan db:seed --force || true) && php artisan config:cache && php artisan route:cache && php artisan serve --host=0.0.0.0 --port=10000
