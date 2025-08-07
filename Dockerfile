# Etapa 1: build de assets y dependencias
FROM node:18 as build

RUN apt-get update && \
    apt-get install -y php-cli php-mbstring php-xml php-mysqli php-mysql

WORKDIR /app

COPY . /app/

# Use Composer official image to install dependencies
RUN composer install --no-dev --optimize-autoloader

RUN npm ci && npm run build
WORKDIR /app

COPY --from=build /app /app

RUN composer install --no-dev --optimize-autoloader

# Continue with Node build
FROM node:18 as nodebuild

WORKDIR /app

COPY --from=composer /app /app
# Ensure package.json has a "build" script before running this
RUN if grep -q '"build"' package.json; then npm ci && npm run build; else npm ci; fi
RUN npm ci && npm run build

# Etapa 2: servidor Apache + PHP
FROM php:8.2-apache
RUN apt-get update && \
    apt-get install -y libzip-dev libicu-dev libonig-dev && \
    docker-php-ext-install pdo_mysql mysqli mbstring

RUN a2enmod rewrite

RUN echo "Listen 8080" > /etc/apache2/ports.conf

COPY apache-site.conf /etc/apache2/sites-available/000-default.conf

# Copy built frontend assets
COPY --from=build /app/dist/ /var/www/html/
EXPOSE 80
COPY --from=build /app/ /var/www/html/

EXPOSE 80 8080

CMD ["apache2-foreground"]
