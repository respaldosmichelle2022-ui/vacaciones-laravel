# --- Etapa 1: Compilar Activos de Vite ---
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- Etapa 2: Aplicación PHP + Nginx ---
FROM php:8.2-fpm-alpine

# Instalar dependencias del sistema y extensiones de PHP necesarias
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libzip-dev \
    postgresql-dev

# Instalar extensiones de PHP (incluyendo pdo_pgsql para Supabase)
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Configurar límites de subida de archivos en PHP
RUN echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini

# Copiar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código del proyecto
COPY . .

# Copiar los archivos compilados por Node de la Etapa 1
COPY --from=node-builder /app/public/build ./public/build

# Instalar dependencias de Composer para producción
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Ajustar permisos para las carpetas de almacenamiento, caché de Laravel y subidas
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads

# Ajustar permisos para que Nginx pueda escribir en directorios temporales y logs ejecutándose como www-data
RUN chown -R www-data:www-data /var/lib/nginx /var/log/nginx

# Copiar configuraciones de Nginx y Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiar el entrypoint y darle permisos de ejecución
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exponer el puerto que Render asignará (por defecto suele ser 10000, pero Render usa la variable PORT)
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
