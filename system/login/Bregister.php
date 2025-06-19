<?php
session_start();
require __DIR__ . '/../resources/database.php';   // conexión PDO
// 1) Recoger y sanitizar
$email    = filter_input(INPUT_POST, 'Email',    FILTER_SANITIZE_EMAIL);
$password = trim($_POST['Password'] ?? '');
// $password = PASSWORD_HASH($_POST['Password'], PASSWORD_DEFAULT);
$nombre = trim($_POST['Nombre'] ?? '');
$apellido = trim($_POST['Apellido'] ?? '');
$fecha_nacimiento = trim($_POST['FechaNacimiento'] ?? '');
$estado= '3';

// 2) Validaciones básicas
if (!$email || !$password || !$nombre || !$apellido || !$fecha_nacimiento) {
    $_SESSION['error'] = 'Faltan datos obligatorios' . "-" . "ERROR 588";
    header('location: /register');
    exit;
}

// 3) Consulta segura
//-------
// $query = $con->prepare("INSERT INTO `useracct` (`email`, `password`, `name`) VALUES (?,?,?)");
// $email = $_POST["email"];
// $psswd = PASSWORD_HASH($_POST["passd"], PASSWORD_DEFAULT);
// $name = $_POST["name"];

// $query->bind_param('bbb', $email, $psswd, $name);
// if ($query->execute()) {
//     echo "Query executed.";
// } else {
//     echo "Query error.";
// }
//-------
// estado = 'activo','inactivo','espera' === 1, 2, 3
// INSERT INTO `usuarios` 
// (`username`, `password`, `nombre`, `apellido`, `estado`, `fecha_nacimiento`) 
// VALUES ("juan1@example.com", "wassword","juan2","Suarez3",1, "2001-12-08")
$sql = "INSERT INTO `usuarios` (`username`, `password`, `nombre`, `apellido`, `estado`, `fecha_nacimiento`) 
VALUES (?,?,?,?,?,?)";
$stmt = $pdo->prepare($sql);
// $stmt->execute([':username' => $email],);
// $query->bind_param('bbbbbb', $email, $password, $nombre, $apellido, $estado, $fecha_nacimiento);
if ($stmt->execute([$email, $password, $nombre, $apellido, $estado, $fecha_nacimiento])) {
    $_SESSION['error'] = 'Usuario creado, inicie sesión' . "-" . "NO ERROR";
    header('Location: /');

} else {
    $_SESSION['error'] = 'Ocurrió un error, inesperado' . "-" . "ERROR 580";
    header('Location: /register');

}

