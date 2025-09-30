# syntax=docker/dockerfile:1

ARG PHP_VERSION=8.2
FROM docker.io/library/php:${PHP_VERSION}-fpm

LABEL "language"="php"

ENV APP_ENV=prod
ENV APP_DEBUG=false

WORKDIR /var/www

# install-php-extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync

# apt dependencies
RUN set -eux \
		&& apt update \
		&& apt install -y cron curl gettext git grep libicu-dev nginx pkg-config unzip \
		&& rm -rf /var/www/html \
		&& rm -rf /var/lib/apt/lists/*

# composer and php extensions
RUN install-php-extensions @composer bcmath gd intl mysqli opcache pcntl pdo_mysql sysvsem zip

# PHP configuration to handle deprecations
RUN echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT" >> /usr/local/etc/php/php.ini
RUN echo "display_errors = Off" >> /usr/local/etc/php/php.ini
RUN echo "log_errors = On" >> /usr/local/etc/php/php.ini

# nginx configuration with better static file handling
RUN cat <<'EOF' > /etc/nginx/sites-enabled/default
server {
    listen 8080;
    root /var/www;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;
    charset utf-8;

    # Static files - handle CSS and JS with correct MIME types
    location ~* \.(css)$ {
        add_header Content-Type "text/css";
        add_header Cache-Control "public, max-age=31536000";
        try_files $uri =404;
    }

    location ~* \.(js)$ {
        add_header Content-Type "application/javascript";
        add_header Cache-Control "public, max-age=31536000";
        try_files $uri =404;
    }

    # Other static files
    location ~* \.(png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # Special handling for src folder (development assets)
    location ^~ /src/ {
        try_files $uri =404;
    }

    # Special handling for js folder
    location ^~ /js/ {
        try_files $uri =404;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_hide_header X-Powered-By;
    }

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    error_log /dev/stderr;
    access_log /dev/stderr;
}
EOF

# project directory
RUN chown -R www-data:www-data /var/www
COPY --link --chown=www-data:www-data --chmod=755 . /var/www

# Ensure correct permissions for static files
RUN find /var/www -type f -name "*.css" -exec chmod 644 {} \;
RUN find /var/www -type f -name "*.js" -exec chmod 644 {} \;
RUN find /var/www -type f -name "*.php" -exec chmod 644 {} \;

# install only PHP dependencies
USER www-data
RUN composer install --optimize-autoloader --classmap-authoritative --no-dev

USER root

CMD nginx; php-fpm;

EXPOSE 8080