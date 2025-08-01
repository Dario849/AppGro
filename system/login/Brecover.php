<?php
require_once 'system/resources/phpMailer.php'; // función externa de envío
require __DIR__ . '/../resources/database.php';   // conexión PDO
session_start();
$email = $_POST['email'] ?? '';
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    $nuevaClave = bin2hex(random_bytes(8)); //Variable con nueva clave para contraseña
    $hashClave = password_hash($nuevaClave, PASSWORD_DEFAULT); //Variable con nueva clave se hashea para ser almacenada en Base de Datos

    $upd = $pdo->prepare("UPDATE usuarios SET password = ? WHERE username = ?");
    $upd->execute([$hashClave, $email]);

    enviarMailRecuperacion($email, $nuevaClave); // función definida externamente, envia a $email la clave (sin hashear) para que el usuario pueda ingresar
} else {
    $_SESSION['error'] = 'El correo no está registrado.-ERROR 104';
    header('Location: /user/recover'); // o a donde necesites
    exit;
}
$_SESSION['success'] = 'Recibirás un correo para restablecer tu contraseña.-REQUEST COMPLETE';
header('Location: /');
exit;
?>