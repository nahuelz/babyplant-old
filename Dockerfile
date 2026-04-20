FROM php:7.1-apache

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Instalar mysqli
RUN docker-php-ext-install mysqli

# Copiar tu código
COPY . /var/www/html

# Asignar permisos
RUN chown -R www-data:www-data /var/www/html
