# ============================
# 1) Etapa de build con Node.js
# ============================
FROM node:20-alpine AS build
WORKDIR /app

# Copiamos package.json y package-lock.json
COPY package*.json ./

# Instalamos dependencias JS
RUN npm install

# Copiamos todo el código fuente
COPY . .

# Compilamos la app con Vite
RUN npm run build


# ============================
# 2) Etapa final: PHP + Composer + Nginx
# ============================
FROM php:8.3-fpm-alpine

# Instalamos dependencias de sistema
RUN apk add --no-cache \
    nginx \
    bash \
    git \
    unzip \
    curl \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql

# Instalamos Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configuración básica de Nginx
COPY <<EOF /etc/nginx/http.d/default.conf
server {
    listen 8080;
    root /var/www/html;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        try_files \$uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }

    location ~* \.(css|js|ico|png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

WORKDIR /var/www/html

# Copiamos archivos del build de Node
COPY --from=build /app/dist ./dist
COPY --from=build /app/index.php ./index.php
COPY --from=build /app/system ./system
COPY --from=build /app/configs ./configs

# Copiamos composer.json y composer.lock
COPY --from=build /app/composer.* ./

# Instalamos dependencias PHP en producción
RUN composer install --no-dev --optimize-autoloader

# Copiamos el resto del código fuente PHP (por ej. app/, vendor se genera con composer)
COPY --from=build /app/vendor ./vendor

# Permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"
