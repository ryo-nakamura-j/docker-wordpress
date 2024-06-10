FROM php:7.1-fpm-alpine


RUN touch /var/log/error_log

ADD ./www.conf /usr/local/etc/php-fpm.d/www.conf

RUN addgroup -g 1000 wp && adduser -G wp -g wp -s /bin/sh -D wp

RUN mkdir -p /var/www/html

RUN chown wp:wp /var/www/html

WORKDIR /var/www/html

# RUN docker-php-ext-install mysqli pdo pdo_mysql calendar && docker-php-ext-enable pdo_mysql && docker-php-ext-configure calendar
RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar

RUN chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp