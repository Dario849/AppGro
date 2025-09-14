<?php
session_start();
require __DIR__ . '/../resources/database.php';   // conexión PDO
// 1) Recoger y sanitizar
$email = filter_input(INPUT_POST, 'Email', FILTER_SANITIZE_EMAIL);
$password = trim($_POST['Password'] ?? '');
// 2) Validaciones básicas
if (!$email || !$password) {
    $_SESSION['error'] = 'Faltan datos obligatorios' . "-" . "ERROR 588";
    header('location: /');
    exit;
}
// 3) Consulta segura
$sql = "SELECT id, password, username FROM usuarios WHERE username = :username LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':username' => $email], );
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user && password_verify($password, $user['password'])) {
    // 4) Credenciales OK
    $_SESSION['logged'] = true;
    $_SESSION['cookie'] = $_COOKIE['PHPSESSID'];
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['username'];
    header('Location: /dashboard');
    exit;
} else {
    // 5) Credenciales FAIL
    $_SESSION['error'] = 'Email o contraseña incorrectos' . "-" . "ERROR 589";
    header('Location: /');
    exit;
}
