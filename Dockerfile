FROM php:8.1-apache

# Install ekstensi MySQLi dan pdo_mysql yang dibutuhkan CI3
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable module rewrite, env, headers Apache untuk (.htaccess / URL rewrite CI3 + HTTPS proxy)
RUN a2enmod rewrite env headers

# Set ServerName untuk menghindari warning FQDN
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Copy seluruh file proyek ke folder web root Apache
COPY . /var/www/html/

# Buat direktori session (Solusi Issue 2) dan set permission folder
RUN mkdir -p /var/www/html/application/cache/sessions \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/application/cache
EXPOSE 80