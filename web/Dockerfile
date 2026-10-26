FROM php:8.2-apache

# Cai extension PDO MySQL va mysqli
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Bat mod_rewrite cho Apache
RUN a2enmod rewrite

# Copy ma nguon
COPY . /var/www/html/

# Cap quyen cho www-data
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Apache lang nghe cong 80
EXPOSE 80
