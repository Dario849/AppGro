<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function enviarMailRecuperacion(string $emailDestino, string $claveTemporal): void
{
    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP (oculta del resto del sistema)
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];

        // Datos del mensaje
        $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_NAME']);
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