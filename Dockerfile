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

# Run composer install to get the MongoDB library
WORKDIR /var/www/html
RUN composer install --no-interaction --optimize-autoloader

# Expose the port Render expects
EXPOSE 80
