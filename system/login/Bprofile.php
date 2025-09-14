<?php
echo "Back de perfil, cambio de contraseña/mail";
session_start();
require __DIR__ . '/../resources/database.php';   // conexión PDO
// 1) Recoger y sanitizar
$emailPassConfirm = trim($_POST['confirmPass'] ?? '');
$emailNew = filter_input(INPUT_POST, 'newMail', FILTER_SANITIZE_EMAIL);
$passOld = trim($_POST['current-password'] ?? '');
$passNew1 = trim($_POST['new-password'] ?? '');
$passNew2 = trim($_POST['new-password-confirm'] ?? '');
// 2) Validaciones básicas
if ( !$passNew1 != !$passNew2) {
    $_SESSION['error'] = 'La nueva contraseña no coincide con la confirmada - Intente denuevo';
    header('location: /user/profile');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['submitButtonPassword'])) {
        // Se envió el formulario de cambio de contraseña
        // Lógica para cambiar contraseña
        try {
            passwordChange($pdo, $_SESSION['user_id'],$passOld, $passNew2);
            header('location: /user/profile');
        } catch (\Throwable $th) {
            $_SESSION['error'] = 'Ocurrió un error al intentar realizar la operación - ERROR 283';
            header('location: /user/profile');

        }
        exit;
    } elseif (isset($_POST['submitButtonEmail'])) {
        // Se envió el formulario de cambio de email
        // Lógica para cambiar email
        try {
            emailChange($pdo, $_SESSION['user_id'],$emailPassConfirm, $emailNew);
            header('location: /user/profile');
        } catch (\Throwable $th) {
            $_SESSION['error'] = 'Ocurrió un error al intentar realizar la operación - ERROR 284';
            header('location: /user/profile');

        }
        exit;

    } else {
        echo "Acción no reconocida";
    }

}
function passwordChange(PDO $pdo, int $userId, string $passwordActual, string $nuevaPassword): bool
{
    try {
        // 1. Traer hash actual
        $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "Usuario no encontrado. - Intente denuevo";
            return false;
        }

        // 2. Verificar contraseña actual
        if (!password_verify($passwordActual, $user['password'])) {
            $_SESSION['error'] = "Contraseña actual incorrecta. - Intente denuevo";
            return false;
        }

        if ($passwordActual === $nuevaPassword) {
            $_SESSION['error'] = "La nueva contraseña no puede ser: - igual a la actual.";
            return false;
        }

        if (strlen($nuevaPassword) < 8 || 
            !preg_match('/[A-Z]/', $nuevaPassword) || 
            !preg_match('/[a-z]/', $nuevaPassword) || 
            !preg_match('/[0-9]/', $nuevaPassword)) {
            $_SESSION['error'] = "La nueva contraseña debe tener: - al menos 8 caracteres, una mayúscula, una minúscula y un número.";
            return false;
        }

        // 4. Generar nuevo hash
        $nuevoHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

        // 5. Actualizar
        $stmt = $pdo->prepare("UPDATE usuarios SET password = :nueva WHERE id = :id");
        if ($stmt->execute([':nueva' => $nuevoHash, ':id' => $userId])) {
            $_SESSION['success'] = "Éxito - Contraseña actualizada correctamente.";
            return true;
        } else {
            $_SESSION['error'] = "No se pudo actualizar la contraseña. - ERROR 999";
            return false;
        }

    } catch (PDOException $e) {
        error_log("[ERROR] Cambio de contraseña: " . $e->getMessage());
        $_SESSION['error'] = "Error inesperado al cambiar la contraseña.  - Intente denuevo";
        return false;
    }
}
function emailChange(PDO $pdo, int $userId, string $passwordActual, string $nuevoEmail): bool
{
    try {
        // 1. Traer hash actual
        $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "Usuario no encontrado. - Intente denuevo";
            return false;
        }

        // 2. Verificar contraseña actual
        if (!password_verify($passwordActual, $user['password'])) {
            $_SESSION['error'] = "Contraseña actual incorrecta. - Intente denuevo";
            return false;
        }
        // 5. Actualizar
        $stmt = $pdo->prepare("UPDATE usuarios SET username = :nueva WHERE id = :id");
        if ($stmt->execute([':nueva' => $nuevoEmail, ':id' => $userId])) {
            $_SESSION['success'] = "Éxito - Email actualizado correctamente.";
            return true;
        } else {
            $_SESSION['error'] = "No se pudo actualizar el Email. - ERROR 999";
            return false;
        }

    } catch (PDOException $e) {
        error_log("[ERROR] Cambio de Email: " . $e->getMessage());
        $_SESSION['error'] = "Error inesperado al cambiar Email.  - Intente denuevo";
        return false;
    }
}
?>