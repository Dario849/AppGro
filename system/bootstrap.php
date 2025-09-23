<?php
// Robust autoloader path resolution
$autoloader_paths = [
    __DIR__ . '/../vendor/autoload.php',
    '/var/www/vendor/autoload.php',
    $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'
];

foreach ($autoloader_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

use Dotenv\Dotenv;

// Robust dotenv path resolution
$env_paths = [
    __DIR__ . '/..',
    '/var/www',
    $_SERVER['DOCUMENT_ROOT']
];

foreach ($env_paths as $path) {
    if (file_exists($path . '/.env') || file_exists($path . '/.env.example')) {
        $dotenv = Dotenv::createImmutable($path); 
        $dotenv->safeLoad();
        break;
    }
}
