# Usa a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Copia todos os arquivos do projeto
COPY . /var/www/html/

# Permissões corretas
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Porta padrão
EXPOSE 80
