<?php
function sessionAuth()
{
    session_start();
    // 1. Verificar que el usuario haya iniciado sesión
    if (!isset($_SESSION['user_id'])) {// No logueado: redirigir al login o página pública
        $_SESSION['error'] = 'Inicie sesión primero' . "-" . "ERROR 589";
        header("Location: /");
        exit();
    }
    // 2. Verificar que el usuario sea administrador
    $esAdmin = ($_SESSION['user_id'] == 1);  // comprueba que sea primer usuario del sistema (ADMIN)
    if (!$esAdmin) {// Intento de acceso de un usuario no autorizado al panel admin
        http_response_code(403);
        $_SESSION['error'] = 'Acceso denegado - Área solo para administradores';
        header("Location: /dashboard");
        exit();
    }
}
?>