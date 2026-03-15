FROM php:8.2-apache

# Install MySQLi extension
RUN apt-get update && apt-get install -y \
    libmariadb-dev \
    mariadb-client \
    && docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy source code to the container
COPY src/ /var/www/html/
COPY game_template.sql /var/www/game_template.sql

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
