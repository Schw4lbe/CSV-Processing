# Use an official PHP image with Apache
FROM php:8.2.12-apache

# Install extensions (e.g., MySQL PDO extension)
RUN docker-php-ext-install pdo pdo_mysql

# Copy your PHP files to the Apache server directory
COPY . /var/www/html/

# create logs directory
RUN mkdir -p /var/www/html/logs && \
    chown -R www-data:www-data /var/www/html/logs

# Expose port 80 to the host
EXPOSE 80