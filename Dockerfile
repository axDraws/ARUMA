# Usamos una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalamos extensiones de PHP comunes (por si tu proyecto usa bases de datos)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiamos los archivos de tu proyecto a la carpeta que Apache sirve por defecto
COPY . /var/www/html/

# Damos permisos a la carpeta para que no haya problemas en Linux/Windows
RUN chown -R www-data:www-data /var/www/html
