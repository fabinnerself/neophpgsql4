FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Configurar Apache para Render
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Crear directorio y copiar archivos
WORKDIR /var/www/html
COPY . .

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Render usa el puerto dinámico $PORT
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/var/log/apache2
ENV APACHE_PID_FILE=/var/run/apache2.pid
ENV APACHE_RUN_DIR=/var/run/apache2
ENV APACHE_LOCK_DIR=/var/lock/apache2

# Exponer puerto para Render
EXPOSE 80

# Script de inicio para usar el puerto dinámico de Render
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]