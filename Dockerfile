FROM php:8.1-apache

# 1. Install dependensi sistem (git & unzip dibutuhkan Composer)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# 2. Copy binary Composer dari image resmi Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable module rewrite, env, headers Apache untuk (.htaccess / URL rewrite CI3 + HTTPS proxy)
RUN a2enmod rewrite env headers

# Set ServerName untuk menghindari warning FQDN
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# 3. Copy seluruh file proyek ke folder web root Apache
COPY . /var/www/html/

# 4. Jalankan composer install untuk mengunduh vendor (AWS SDK, dll.)
RUN composer install --no-dev --optimize-autoloader

# Buat direktori session dan set permission folder
RUN mkdir -p /var/www/html/application/cache/sessions \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/application/cache

EXPOSE 80