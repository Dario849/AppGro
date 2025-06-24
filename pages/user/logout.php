<?php
session_start();                    // Reanudar la sesión existente
$_SESSION = [];                     // 1. Vaciar datos de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    // 2. Borrar cookie de sesión en el navegador
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
session_destroy();                  // 3. Destruir la sesión en el servidor
header("Location: /");      // Redirigir a página pública
exit;
