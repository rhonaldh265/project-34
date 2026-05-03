FROM php:8.2-apache

# Install dependencies and the MongoDB extension
RUN apt-get update && apt-get install -y \
    libssl-dev \
    unzip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . /var/www/html/
WORKDIR /var/www/html

# Run composer install (Standard version)
RUN composer install --no-interaction --optimize-autoloader

# Set the homepage to signup.php
RUN echo "DirectoryIndex signup.php index.php" >> /etc/apache2/apache2.conf

EXPOSE 80
