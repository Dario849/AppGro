<?php
function sessionCheck()
{
    session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = 'Inicie sesión primero' . "-" . "ERROR 589";
        header("Location: /");
        exit;
    }
}
