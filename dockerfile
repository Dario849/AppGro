FROM node:20-alpine AS build
LABEL "language"="php"
LABEL "framework"="vite"

WORKDIR /app

# Copy package files and install dependencies
COPY package*.json ./
RUN npm install

# Copy source code 
COPY . .

# Create optimized vite config for PHP integration
RUN cat > vite.prod.config.js << 'EOF'
import { defineConfig } from 'vite';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    base: './',
    build: {
        rollupOptions: {
            input: {
                main: 'src/main.js',
            },
        },
        assetsDir: 'assets',
        emptyOutDir: true,
        outDir: 'dist',
        manifest: true,
        cssCodeSplit: false
    },
    plugins: [
        viteStaticCopy({
            targets: [
                { src: 'public', dest: '' },
                { src: 'system', dest: '' },
                { src: 'configs', dest: '', overwrite: false },
                { src: 'vendor', dest: '' },
            ],
            silent: true,
        }),
        tailwindcss(),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler',
            },
        },
    },
});
EOF

# Build with optimized config
RUN npx vite build --config vite.prod.config.js

# Production stage
FROM php:8.3-fpm-alpine
LABEL "language"="php"

# Install nginx and PHP extensions
RUN apk add --no-cache nginx && \
    docker-php-ext-install pdo pdo_mysql

# Configure nginx with proper asset serving
COPY <<EOF /etc/nginx/http.d/default.conf
server {
    listen 8080;
    root /var/www/html;
    index index.php index.html;

    # Handle static assets (CSS, JS, images)
    location /dist/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files \$uri =404;
    }

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

# Copy application files and built assets
COPY --from=build /app .

# Create PHP helper for assets
RUN cat > asset-helper.php << 'EOF'
<?php
function getViteAssets() {
    $manifestPath = __DIR__ . '/dist/manifest.json';
    
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        
        $assets = '';
        foreach ($manifest as $key => $asset) {
            if (isset($asset['css'])) {
                foreach ($asset['css'] as $css) {
                    $assets .= '<link rel="stylesheet" href="/dist/' . $css . '">' . "\n";
                }
            }
            if (isset($asset['file']) && str_ends_with($asset['file'], '.js')) {
                $assets .= '<script type="module" src="/dist/' . $asset['file'] . '"></script>' . "\n";
            }
        }
        
        return $assets;
    }
    
    return '<link rel="stylesheet" href="/dist/assets/main.css">';
}
?>
EOF

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"