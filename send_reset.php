<?php
// PHP/send_reset.php
session_start();
require_once __DIR__ . '/Base de datos conexion.php';
// Cargar configuración de correo 
$mailConfig = [];
if (file_exists(__DIR__ . '/config_mail.php')) {
    $cfg = include __DIR__ . '/config_mail.php';
    if (is_array($cfg)) $mailConfig = $cfg;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Método no permitido');
}

$email = trim($_POST['email'] ?? '');
if ($email === '') {
    $_SESSION['fp_message'] = 'Introduce un correo válido.';
    header('Location: ../Forgot.php');
    exit;
}

try {
    $pdo = getPDO();

    // Asegurar columnas para token/expiración (si no existen)
    $colsStmt = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'usuarios' AND column_name IN ('password_reset_token','password_reset_expires')");
    $colsStmt->execute();
    $found = $colsStmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('password_reset_token', $found, true)) {
        $pdo->exec("ALTER TABLE usuarios ADD COLUMN password_reset_token VARCHAR(255) NULL");
    }
    if (!in_array('password_reset_expires', $found, true)) {
        $pdo->exec("ALTER TABLE usuarios ADD COLUMN password_reset_expires DATETIME NULL");
    }

    $stmt = $pdo->prepare("SELECT id, nombre, username FROM usuarios WHERE username = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // No decir explícitamente que no existe por seguridad, pero en dev mostramos mensaje.
        $_SESSION['fp_message'] = 'Si el correo existe, se generó un enlace de restablecimiento.';
        header('Location: ../Forgot.php');
        exit;
    }

    // Generar token
    $token = bin2hex(random_bytes(16));
    $expires = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

    $upd = $pdo->prepare("UPDATE usuarios SET password_reset_token = ?, password_reset_expires = ? WHERE id = ?");
    $upd->execute([$token, $expires, $user['id']]);

    // Generar enlace (en producción se enviaría por correo)
    $resetLink = sprintf('%s://%s%s/ResetPassword.php?token=%s',
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
        $_SERVER['HTTP_HOST'],
        dirname($_SERVER['REQUEST_URI']),
        $token
    );

    // Intentar enviar por correo si la configuración lo permite
    $emailSent = false;
    $emailError = '';

    // Construir el cuerpo del correo
    $bodyHtml = '<p>Hola ' . htmlspecialchars($user['nombre']) . ',</p>' .
        '<p>Hemos recibido una solicitud para restablecer tu contraseña. Haz clic en el siguiente enlace (o cópialo en tu navegador):</p>' .
        '<p><a href="' . htmlspecialchars($resetLink) . '">' . htmlspecialchars($resetLink) . '</a></p>' .
        '<p>Si no solicitaste esto, ignora este correo.</p>' .
        '<p>Atentamente,<br/>BusEdu</p>';

    // 1) Si PHPMailer está disponible vía Composer, usarlo
    $composerAutoload = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($composerAutoload)) {
        require_once $composerAutoload;
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            if (!empty($mailConfig['use_smtp'])) {
                $mail->isSMTP();
                $mail->Host = $mailConfig['smtp_host'];
                $mail->Port = $mailConfig['smtp_port'];
                if (!empty($mailConfig['smtp_secure'])) {
                    $mail->SMTPSecure = $mailConfig['smtp_secure'];
                }
                $mail->SMTPAuth = true;
                $mail->Username = $mailConfig['smtp_username'];
                $mail->Password = $mailConfig['smtp_password'];
            }

            $mail->setFrom($mailConfig['from_email'] ?? 'no-reply@busedu.local', $mailConfig['from_name'] ?? 'BusEdu');
            $mail->addAddress($user['username'], $user['nombre']);
            $mail->isHTML(true);
            $mail->Subject = $mailConfig['reset_subject'] ?? 'Restablece tu contraseña en BusEdu';
            $mail->Body = $bodyHtml;
            $mail->AltBody = strip_tags(str_replace(['<br/>','<br>','</p>','</div>'], "\n", $bodyHtml));

            $mail->send();
            $emailSent = true;
        } catch (\Exception $e) {
            $emailSent = false;
            $emailError = $e->getMessage();
        }
    }

    // 2) Si no se envió y la config indica usar SMTP sin Composer, intentar mail() con headers
    if (!$emailSent) {
        // Construir headers
        $to = $user['username'];
        $subject = $mailConfig['reset_subject'] ?? 'Restablece tu contraseña en BusEdu';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: " . ($mailConfig['from_name'] ?? 'BusEdu') . " <" . ($mailConfig['from_email'] ?? 'no-reply@busedu.local') . ">\r\n";

        // Intento de envío con mail() (puede fallar en entornos locales sin SMTP)
            try {
                $ok = mail($to, $subject, $bodyHtml, $headers);
                if ($ok) {
                    $emailSent = true;
                } else {
                    $emailSent = false;
                    $emailError = 'mail() returned false';
                }
            } catch (\Exception $e) {
                $emailSent = false;
                $emailError = $e->getMessage();
            }
    }

    if ($emailSent) {
        $_SESSION['fp_message'] = 'Se ha enviado un correo con instrucciones para restablecer tu contraseña. Revisa tu bandeja de entrada.';
        header('Location: ../Forgot.php');
        exit;
    }

    // Si no se pudo enviar por correo, mostrar el enlace en pantalla (fallback de desarrollo)
    $_SESSION['fp_message'] = 'No fue posible enviar el correo automáticamente. Copia el enlace mostrado abajo.';

    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Enlace de restablecimiento - BusEdu</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen flex items-center justify-center p-4 bg-gray-50">
        <div class="w-full max-w-2xl bg-white p-6 rounded shadow">
            <h1 class="text-xl font-bold mb-4">Enlace de restablecimiento generado</h1>
            <p class="mb-4">No se pudo enviar el correo automáticamente. Copia el siguiente enlace y pégalo en tu navegador para restablecer la contraseña:</p>
            <div class="bg-gray-100 p-4 rounded break-words"> <a href="<?php echo htmlspecialchars($resetLink); ?>"><?php echo htmlspecialchars($resetLink); ?></a> </div>
            <p class="mt-4 text-sm text-red-600">Error de envío: <?php echo htmlspecialchars($emailError); ?></p>
            <p class="mt-4"><a href="../Login.php" class="text-purple-600">Volver al login</a></p>
        </div>
    </body>
    </html>
    <?php
    exit;

} catch (\Exception $e) {
    $_SESSION['fp_message'] = 'Error interno. Intenta más tarde.';
    header('Location: ../Forgot.php');
    exit;
}
