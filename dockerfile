# Use the official PHP image with Apache
FROM php:8.0-apache

# Install necessary extensions and tools
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql zip

# Set the working directory
WORKDIR /var/www/html/

# Copy the composer files
COPY composer.json composer.lock ./

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy the application source code
COPY  ./src ./src

# Expose port 80
EXPOSE 80
