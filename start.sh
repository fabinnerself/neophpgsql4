#!/bin/bash

# Configurar Apache para usar el puerto dinámico de Render
sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT:-80}>/g" /etc/apache2/sites-available/000-default.conf

# Iniciar Apache
apache2-foreground