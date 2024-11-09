# Usa una imagen base de PHP con Composer
FROM php:8.2-fpm

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo
WORKDIR /var/www

# Copia el contenido de la aplicación Laravel
COPY . .

# Configura permisos (importante para evitar problemas de permisos)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Expone el puerto 8000 para PHP
EXPOSE 8000

# Comando para iniciar Laravel en el puerto 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
