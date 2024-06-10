FROM php:7.4.33-fpm

RUN apt-get update

RUN touch /var/log/error_log

ADD ./www.conf /usr/local/etc/php-fpm.d/www.conf

RUN docker-php-ext-install pdo pdo_mysql mysqli
