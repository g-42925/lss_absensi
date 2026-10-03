FROM php:8.1-apache

# Install ekstensi MySQLi dan pdo_mysql yang dibutuhkan CI3
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable module rewrite Apache untuk (.htaccess / URL rewrite CI3)
RUN a2enmod rewrite

# Copy seluruh file proyek ke folder web root Apache
COPY . /var/www/html/

# Set permission folder
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80