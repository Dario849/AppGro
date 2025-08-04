<?php
// Si se está usando el servidor embebido de PHP
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    if (is_file($file)) {
        return false; // Servir el archivo tal cual
    }
}
// Pasar todo a index.php
require __DIR__ . '/index.php';
