FROM php:8.2-apache

# Cài extension cần cho Laravel
RUN docker-php-ext-install pdo pdo_mysql

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy source code
COPY . /var/www/html

WORKDIR /var/www/html

# Cài composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --optimize-autoloader --no-dev

# Set document root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80