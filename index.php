<?php

// Initial setup

define('ROOT', __DIR__);
define('MODE_DEV', '%MODE%' === 'development');

function require_existing(string $path) {
	file_exists($path) && require_once($path);
}

require_existing('vendor/autoload.php');
require_existing('configs/env.php');

try {
	require_existing('configs/routes.php');
} catch (\Throwable $th) {
	die('Error: ' . $th->getMessage());
}

// 1) El autoload de Composer (dotenv + App\Database)
require_once __DIR__ . '/../autoload.php';

use Dotenv\Dotenv;
use App\Database;

// 2) Carga .env
Dotenv::createImmutable(__DIR__ . '/..')->load();
