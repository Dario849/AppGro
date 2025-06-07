<?php
// namespace App\resources;
// class Database{
//     private string $servername = '127.0.0.1';
//     private string $username   = 'root';
//     private string $password   = '';
//     private string  $dbname     = 'app_campo';
//     private ?\mysqli $conn     = null;

//     /**
//      * Establece y devuelve la conexión MySQLi.
//      *
//      * @return \mysqli
//      */
//     public function connect(): \mysqli
//     {
//         if ($this->conn === null) {
//             $this->conn = new \mysqli(
//                 $this->servername,
//                 $this->username,
//                 $this->password,
//                 $this->dbname
//             );

//             if ($this->conn->connect_error) {
//                 die('Connection failed: ' . $this->conn->connect_error);
//             }
//         }

//         return $this->conn;
//     }

// }
declare(strict_types=1);


$dsn = 'mysql:host=127.0.0.1;dbname=app_campo;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    // Aquí creas la instancia global $pdo
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // Manejo de error de conexión
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}