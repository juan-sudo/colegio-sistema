#!/bin/bash
set -e

# Ejecutar migraciones
php artisan migrate --force

# Limpiar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recachear en producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Aplicación lista en http://0.0.0.0:10000"

# Iniciar servidor
php artisan serve --host=0.0.0.0 --port=10000