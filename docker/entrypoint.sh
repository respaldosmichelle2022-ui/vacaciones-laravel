#!/bin/sh

# Salir inmediatamente si algún comando falla
set -e

echo "Optimizando la configuración de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Ejecutando migraciones pendientes..."
# La bandera --force es necesaria porque estamos en producción (APP_ENV=production)
php artisan migrate --force

echo "Ejecutando seeders..."
php artisan db:seed --force

echo "Iniciando Nginx y PHP-FPM a través de Supervisor..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
