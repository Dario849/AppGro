<?php
require_once __DIR__ . '/../bootstrap.php';
class DB
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo)
            return self::$pdo;

        $host = $_ENV['DB_HOST'] ?? null;
        $dbname = $_ENV['DB_NAME'] ?? null;
        $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
        $user = $_ENV['DB_USER'] ?? null;
        $pass = $_ENV['DB_PASS'] ?? null;

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