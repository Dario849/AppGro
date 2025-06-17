<?php
exit('Este archivo es solo un ejemplo.');
// Alarga la vida útil de la cookie, de esta forma se reestablecería el tiempo de sesión (aplica para re-ingresos, etc...)
$lifetime = 30 * 24 * 60 * 60;  // 30 días en segundos
// $days = 50;
// $hoursPerDay = 24;
// $minutesPerHour = 60;
// $secondsPerMinute = 60;

// $lifetime = $days * $hoursPerDay * $minutesPerHour * $secondsPerMinute;
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path'     => '/',                // cookie disponible en toda la app
    'domain'   => $_SERVER['HTTP_HOST'],
    'secure'   => isset($_SERVER['HTTPS']), // true si la conexión es HTTPS
    'httponly' => true
]);
session_start();
?>
<?php
// ... (tras validar usuario y contraseña) ...
$_SESSION['usuario_id'] = $usuario_id;  // guardar identificador en la sesión

if (!empty($_POST['recordar_usuario'])) {
    // 1. Generar un token aleatorio y seguro
    $token = bin2hex(random_bytes(16));  // 16 bytes aleatorios -> string hex de 32 chars

    // 2. Guardar el token en una cookie persistente (p. ej. 30 días)
    setcookie('recordar_usuario', $token, [
        'expires' => time() + (86400 * 30),  // ahora + 30 días
        'path'    => '/',
        'domain'  => $_SERVER['HTTP_HOST'],
        'secure'  => true,   // Solo enviar por HTTPS
        'httponly' => true,   // No accesible vía JavaScript
        'samesite' => 'Strict'
    ]);

    // 3. Almacenar el token en la base de datos asociado al usuario
    $sql = "UPDATE usuarios SET token_login = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$token, $usuario_id]);
}

?> 