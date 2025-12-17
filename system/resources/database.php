<?php
// Robust path resolution for container environments
$bootstrap_paths = [
    __DIR__ . '/../bootstrap.php',
    '/var/www/system/bootstrap.php', 
    $_SERVER['DOCUMENT_ROOT'] . '/system/bootstrap.php'
];

foreach ($bootstrap_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}
class DB
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo)
            return self::$pdo;

$host = $_ENV['MYSQL_HOST'] ?? null;
$dbname = $_ENV['DB_NAME'] ?? null;
$port = $_ENV['MYSQL_PORT'] ?? null;
$dsn = 'mysql:host=' .  $host .';port='. $port . ';dbname=' . $dbname  . ';charset=utf8mb4';
$user = $_ENV['MYSQL_USERNAME'] ?? null;
$pass = $_ENV['MYSQL_PASSWORD'] ?? null;

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return self::$pdo;
        } catch (PDOException $e) {
            http_response_code(500);
            die('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }
}
