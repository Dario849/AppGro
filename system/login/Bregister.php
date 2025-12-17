<?php
session_start();
require __DIR__ . '/../resources/database.php';   // conexión PDO
$pdo = DB::connect();
// 1) Recoger y sanitizar
$email = filter_input(INPUT_POST, 'Email', FILTER_SANITIZE_EMAIL);
// $password = trim($_POST['Password'] ?? ''); // Almacena en variable, el campo sin encriptar
$password = PASSWORD_HASH(trim($_POST['Password'] ?? ''), PASSWORD_DEFAULT); // elimina espacios, hashea / encripta la contraseña
$nombre = trim($_POST['Nombre'] ?? '');
$apellido = trim($_POST['Apellido'] ?? '');
$fecha_nacimiento = trim($_POST['FechaNacimiento'] ?? '');
$estado = '3';

// 2) Validaciones básicas
if (!$email || !$password || !$nombre || !$apellido || !$fecha_nacimiento) {
    $_SESSION['error'] = 'Faltan datos obligatorios' . "-" . "ERROR 588";
    header('location: /user/register');
    exit;
}
try {
    $check = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE username = :username");
    $check->execute([':username' => $email]);
    if ($check->fetchColumn() > 0) {
        $_SESSION['error'] = 'El usuario ya existe.';
        header('Location: /user/register');
        exit;
    } else {
        $sql = "INSERT INTO `usuarios` (`username`, `password`, `nombre`, `apellido`, `estado`, `fecha_nacimiento`) VALUES (?,?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$email, $password, $nombre, $apellido, $estado, $fecha_nacimiento]);
        if ($result) {
            $_SESSION['success'] = 'Usuario creado, inicie sesión' . "-" . "NO ERROR";
            header('Location: /');
        } else {
            $_SESSION['error'] = 'Ocurrió un error, inesperado' . "-" . "ERROR 580";
            header('Location: /user/register');
            exit;
        }
    }
} catch (PDOException $e) {
    // log error
    $_SESSION['error'] = 'Ocurrió un error inesperado...' . "-" . "ERROR 999";
    echo "Error en el registro: " . $e->getMessage();
}

