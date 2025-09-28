<?php
session_start();
require __DIR__ . '/../resources/database.php';   // conexión PDO
try {
    $email = filter_input(INPUT_POST, 'Email', FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['Password'] ?? '');
    if (!$email || !$password) {
        $_SESSION['error'] = 'Faltan datos obligatorios' . "-" . "ERROR 588";
        header('location: /');
        exit;
    }
    $action = $_POST['action'] ?? '';
    $token = $_POST['token'] ?? '';
    $cu = curl_init(); // prepara cURL para llamado a API google reCaptcha
    curl_setopt($cu, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify"); // carga URL
    curl_setopt($cu, CURLOPT_POST, 1); // Declara utilizar método POST
    curl_setopt($cu, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $_ENV['RECAPTCHA_SECRET_KEY'], 'response' => $token))); // Configura campos a añadir la request
    curl_setopt($cu, CURLOPT_RETURNTRANSFER, true); // carga respuesta de la consulta en variable
    $response = curl_exec($cu); // ejecutra consulta, obtiene respuesta
    curl_close($cu); // cierra recurso cURL

    $datos = json_decode($response, true); // decode JSON response

    print_r($datos); // DEBUG, en caso de que no se realize ningun header, se muestra el resultado

    if ($datos['success'] == 1 && $datos['score'] >= 0.5) { // puntuación mínima aceptada 0.5
        if ($datos['action'] == 'validarUsuario') {
            $sql = "SELECT id, password, username FROM usuarios WHERE username = :username AND estado = 'activo' LIMIT 1";
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
        }

    } else {
        $_SESSION['error'] = 'Challenge Captcha falló, intente luego' . "-" . "ERROR 418";
        header('Location: /');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Error interno, intente luego' . "-" . "ERROR 500";
    header('Location: /');
    exit;
}

