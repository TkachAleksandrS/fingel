FROM node:latest AS node
FROM php:8.1-fpm

MAINTAINER Tkach Aleksandr

# Install PHP extentions
RUN apt-get update
RUN apt-get install -y libzip-dev zip
RUN apt-get install -y libcurl4-openssl-dev openssl libonig-dev libxml2-dev
RUN docker-php-ext-configure zip
RUN	docker-php-ext-install zip
RUN docker-php-ext-install curl
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install xml
RUN docker-php-ext-install exif
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql
RUN apt-get install -y libpq-dev
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Install composer and update to newest version
RUN apt-get install -y wget
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --install-dir=/usr/local/bin --filename=composer --quiet
RUN composer selfupdate
RUN php -r "unlink('composer-setup.php');"
RUN apt-get remove -y wget

# Install npm
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

# Install tools
RUN apt-get -y install nano