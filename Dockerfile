FROM php:8.5-apache

RUN docker-php-ext-install pdo_mysql mysqli \
    && a2enmod rewrite \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' \
       /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf

RUN printf 'upload_max_filesize=20M\npost_max_size=20M\nmemory_limit=256M\n' \
    > /usr/local/etc/php/conf.d/app.ini
