<?php
session_start();
require 'system\resources\database.php';   // conexión PDO
// 1) Recoger y sanitizar
$email    = filter_input(INPUT_POST, 'Email',    FILTER_SANITIZE_EMAIL);
$password = trim($_POST['Password'] ?? '');

// 2) Validaciones básicas
if (!$email || !$password) {
    $_SESSION['error'] = 'Faltan datos obligatorios';
    header('location: /');
    exit;
}

// 3) Consulta segura
$sql = "SELECT id, password FROM usuarios WHERE username = :mail LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':mail' => $email],);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // 4) Credenciales OK
    $_SESSION['user_id'] = $user['id'];
    header('Location: /dashboard');
    exit;
} elseif (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'Email o contraseña incorrectos' . "-" . "PASSWORD_VERIFY_ERROR";
    header('Location: /');
} else {
    // 5) Credenciales FAIL
    $_SESSION['error'] = 'Email o contraseña incorrectos' . "-" . "ERROR 589";
    header('Location: /');
    exit;
}
