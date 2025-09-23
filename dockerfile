FROM php:8.3-fpm-alpine AS build
LABEL "language"="php"
LABEL "framework"="vite"

# Install Node.js for building assets
RUN apk add --no-cache nodejs npm

WORKDIR /app

# Copy package files and install dependencies
COPY package*.json ./
RUN npm install

# Copy source code and build assets
COPY . .
RUN npm run build

# Production stage
FROM php:8.3-fpm-alpine
LABEL "language"="php"

# Install nginx and PHP extensions
RUN apk add --no-cache nginx && \
    docker-php-ext-install pdo pdo_mysql

# Configure nginx
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
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
    }

    location ~* \.(css|js|ico|png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

WORKDIR /var/www/html

# Copy application files
COPY --from=build /app .
COPY --from=build /app/dist ./

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"