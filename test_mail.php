<?php
// PHP/test_mail.php - prueba rápida de envío de correo usando config_mail.php
$config = [];
if (file_exists(__DIR__ . '/config_mail.php')) {
    $cfg = include __DIR__ . '/config_mail.php';
    if (is_array($cfg)) {
        $config = $cfg;
    }
}

$to = $config['smtp_username'] ?? ($config['from_email'] ?? 'test@example.com');
$subject = 'Test de correo BusEdu';
$body = '<p>Este es un correo de prueba desde BusEdu</p>';

// Intentar PHPMailer si está disponible
$composer = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composer)) {
    require_once $composer;
    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        if (!empty($config['use_smtp'])) {
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->Port = $config['smtp_port'];
            if (!empty($config['smtp_secure'])) $mail->SMTPSecure = $config['smtp_secure'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
        }
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        echo "Correo enviado correctamente a: " . htmlspecialchars($to);
    } catch (\Exception $e) {
        echo "Error enviando con PHPMailer: " . htmlspecialchars($e->getMessage());
    }
    exit;
}

// Fallback a mail()
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=utf-8\r\n";
$headers .= "From: " . ($config['from_name'] ?? 'BusEdu') . " <" . ($config['from_email'] ?? 'no-reply@busedu.local') . ">\r\n";

if (mail($to, $subject, $body, $headers)) {
    echo "Correo enviado correctamente (mail()) a: " . htmlspecialchars($to);
} else {
    echo "Fallo en mail(). Revisa la configuración SMTP o instala PHPMailer y configura config_mail.php.";
}
