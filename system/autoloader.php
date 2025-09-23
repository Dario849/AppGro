<?php
// Define application root paths for reliable includes
define('APP_ROOT', __DIR__ . '/..');
define('SYSTEM_ROOT', __DIR__);

// Load composer autoloader
require_once APP_ROOT . '/vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(APP_ROOT); 
$dotenv->safeLoad();
