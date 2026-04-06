FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev libpng-dev libzip-dev unzip \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql mysqli gd zip

RUN a2enmod rewrite headers expires deflate

# Copy everything (including public/ with assets & api inside)
COPY . /var/www/html/

# Set document root to /var/www/html/public (which now contains assets & api)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set permissions (recursively)
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]