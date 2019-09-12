FROM php:7.2-fpm

RUN apt-get update
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /usr/src/app

COPY ./ /usr/src/app

#CMD php /usr/src/bin/console doctrine:migrations:migrate