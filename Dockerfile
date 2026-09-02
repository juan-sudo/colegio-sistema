FROM php:8.2-cli

WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
git \
curl \
unzip \
zip \
libpng-dev \
libonig-dev \
libxml2-dev \
libzip-dev \
libjpeg62-turbo-dev \
libfreetype6-dev \
libwebp-dev \
libpq-dev \
ca-certificates \
gnupg \
&& curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
&& apt-get install -y --no-install-recommends nodejs \
&& docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
&& docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip \
&& apt-get clean \
&& rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar proyecto
COPY . /var/www/html

# Laravel necesita estas carpetas creadas y con permisos antes de composer install
RUN mkdir -p /var/www/html/bootstrap/cache /var/www/html/storage/framework/cache /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/logs \
&& chmod -R 775 /var/www/html/bootstrap/cache /var/www/html/storage \
&& chown -R www-data:www-data /var/www/html/bootstrap/cache /var/www/html/storage /var/www/html/public

# Dependencias PHP + assets frontend
RUN composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader --no-dev \
&& npm install \
&& npm run build \
&& chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

EXPOSE 10000

# Nota: config:cache/route:cache/view:cache se ejecutan al iniciar el contenedor (no en build),
# porque las variables de entorno reales (APP_KEY, DB_*, etc.) solo estan disponibles
# en tiempo de ejecucion en Render, no durante la construccion de la imagen.
CMD ["bash", "-c", "php artisan config:clear && php artisan route:clear && php artisan view:clear && mkdir -p database && touch database/database.sqlite && chown www-data:www-data database/database.sqlite && php artisan migrate --force && (php artisan db:seed --force || true) && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
