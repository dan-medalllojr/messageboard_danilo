# load php 8
FROM php:8.1-apache

# install mysql extension for php
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

# add file
ADD ./php/apache2.conf /etc/apache2/apache2.conf

# run mutex
RUN echo "Mutex posixsem" >> /etc/apache2/apache2.conf

# enable apache2 mods needed by nativecamp
RUN a2enmod ssl
RUN a2enmod rewrite

# expose ports
EXPOSE 443
EXPOSE 80