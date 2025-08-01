<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '.config.php'; // ACA SE DEFINEN VARIABLES DE ENTORNO, CLAVE, HOST, USUARIO, DB_NAME

function enviarMailRecuperacion(string $emailDestino, string $claveTemporal): void
{
    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP (oculta del resto del sistema)
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        // Datos del mensaje
        $mail->setFrom(SMTP_FROM, SMTP_NAME);
        $mail->addAddress($emailDestino);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Subject = 'Recuperación de contraseña';
        $mail->Body = "Hola,\n\nHemos restablecido tu contraseña.\n"
            . "Tu nueva contraseña temporal es: $claveTemporal\n\n"
            . "Por seguridad, cámbiala al iniciar sesión.";

        $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        $_SESSION['error'] = "No se pudo enviar el correo: " . $e->getMessage();
    }
}
?>