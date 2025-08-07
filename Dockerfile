# Etapa 1: build de assets y dependencias
FROM node:18 as build

RUN apt-get update && \
    apt-get install -y php-cli php-mbstring php-xml php-mysqli php-mysql && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . /app/

RUN composer install --no-dev --optimize-autoloader && \
    npm ci && npm run build

# Etapa 2: servidor Apache + PHP
FROM php:8.2-apache

RUN apt-get update && \
    apt-get install -y libzip-dev libicu-dev libonig-dev && \
    docker-php-ext-install pdo_mysql mysqli mbstring

RUN a2enmod rewrite

RUN echo "Listen 8080" >> /etc/apache2/ports.conf

COPY apache-site.conf /etc/apache2/sites-available/000-default.conf

COPY --from=build /app/dist/ /var/www/html/

EXPOSE 80 8080

RUN a2ensite 000-default.conf

CMD ["apache2-foreground"]
