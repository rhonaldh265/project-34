# Use an official PHP image with Apache
FROM php:8.2-apache

# Install MongoDB driver dependencies
RUN apt-get update && apt-get install -y \
    libssl-dev \
    unzip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy your files into the web server directory
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Run composer install but ignore the platform check for ext-mongodb
# This bypasses the version mismatch error you saw
RUN composer install --no-interaction --optimize-autoloader --ignore-platform-req=ext-mongodb

# Expose the port Render expects
EXPOSE 80
