<?php
// Log para Railway (stderr)
file_put_contents('php://stderr', "[INDEX] Request: " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);

// Configuración inicial
define('ROOT', __DIR__);
define('MODE_DEV', '%MODE%' === 'development');

function require_existing(string $path) {
    file_exists($path) && require_once($path);
}

require_existing('vendor/autoload.php');

try {
    require_existing('configs/routes.php');
} catch (\Throwable $th) {
    file_put_contents('php://stderr', "[INDEX] ERROR: " . $th->getMessage() . "\n", FILE_APPEND);
    die('Error: ' . $th->getMessage());
}
