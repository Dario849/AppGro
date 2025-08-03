<?php
require_once __DIR__ . '/../bootstrap.php';

$host = $_ENV['DB_HOST'] ?? null;
$dbname = $_ENV['DB_NAME'] ?? null;
$dsn = 'mysql:host=' .  $host . ';dbname=' . $dbname  . ';charset=utf8mb4';
$user = $_ENV['DB_USER'] ?? null;
$pass = $_ENV['DB_PASS'] ?? null;

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}