FROM php:8.0-fpm

ENV TZ Asia/Tokyo

RUN apt-get update 
RUN apt-get install -y git unzip libzip-dev libicu-dev libonig-dev
RUN docker-php-ext-install intl pdo_mysql zip bcmath
		
COPY ./docker/app/php.ini /usr/local/etc/php/php.ini

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

WORKDIR /app