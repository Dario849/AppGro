<?php
require('system/main.php');
renderNavbar($_SESSION['user_id']);

$layout = new HTML(title: 'AppGro-Tareas');

// use App\resources\Database;

// $db   = new Database();
// $conn = $db->connect();

// $sql    = "SELECT nombre FROM Usuarios WHERE id BETWEEN 1 AND 5";
// $result = $conn->query($sql);

// if ($result === false) {
//     die("Error en la consulta: " . $conn->error);
// }
session_start();
?>