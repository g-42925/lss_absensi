FROM php:8.1-apache

# 1. Copy binary Composer langsung dari image resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 2. Install dependensi minimal dan ekstensi MySQLi/PDO
RUN apt-get update && apt-get install -y \
    unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# 3. Apache configurations
RUN a2enmod rewrite env headers
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# 4. Copy seluruh proyek
COPY . /var/www/html/

# 5. Jalankan composer install
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 6. Direktori session dan permission
RUN mkdir -p /var/www/html/application/cache/sessions \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/application/cache

EXPOSE 80