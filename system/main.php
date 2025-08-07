<?php
require_once('partials\layouts\HTML.php'); //Carga componente html, asigna head, body, scripts
require_once __DIR__ . '/bootstrap.php'; //Carga phpdotenv
require_once('partials\navBar.php'); //Carga barra lateral para navegación de toda la página
require_once('partials\weather.php'); //Carga api del clima
require_once('partials\sessionCheck.php'); //Verifica inicio de sesión
require_once('partials\alert.php'); //Carga Sistema de reporte con $success y $error
require_once('partials\auth.php'); //Verifica $uid, si no es admin bloquea acceso a página