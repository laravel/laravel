# Usa una imagen oficial de PHP con extensiones necesarias
FROM php:8.2-fpm

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Configura el directorio de trabajo dentro del contenedor
WORKDIR /var/www

# Copia los archivos del proyecto al contenedor
COPY . .

# Instala Composer y las dependencias del proyecto
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Expone el puerto que usa Laravel
EXPOSE 8000

# Comando para ejecutar Laravel cuando el contenedor inicie
CMD php artisan serve --host=0.0.0.0 --port=8000
