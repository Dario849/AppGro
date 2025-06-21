<?php
declare(strict_types=1);
require_once '.config.php'; // ACA SE DEFINEN VARIABLES DE ENTORNO, CLAVE, HOST, USUARIO, DB_NAME


$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$user = DB_USER;
$pass = DB_PASS;

try {
    // Aquí creas la instancia global $pdo
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // Manejo de error de conexión
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}