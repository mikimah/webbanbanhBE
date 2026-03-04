FROM php:8.2-apache

# Cài các package hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath zip exif

# Enable rewrite
RUN a2enmod rewrite

# Set document root về public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Copy project
COPY . /var/www/html

WORKDIR /var/www/html

# Copy composer từ image chính thức
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

EXPOSE 80