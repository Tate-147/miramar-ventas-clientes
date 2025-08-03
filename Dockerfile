# Usar una imagen oficial de PHP 8.2
FROM php:8.2-fpm-alpine

# Instalar SOLAMENTE las extensiones de PHP para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Instalar Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos de la aplicación
COPY . .

# Instalar las dependencias de la aplicación
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

# Exponer el puerto
EXPOSE 8080

# Corregir permisos para que el servidor pueda escribir en los logs
RUN chown -R www-data:www-data /var/www/html/storage

# Comando para iniciar el servidor de PHP
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]