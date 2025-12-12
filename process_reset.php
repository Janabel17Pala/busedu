<?php
// PHP/process_reset.php
session_start();
require_once 'Base de datos conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Método no permitido');
}

$token = trim($_POST['token'] ?? '');
$password = trim($_POST['password'] ?? '');
$password_confirm = trim($_POST['password_confirm'] ?? '');

if ($token === '' || $password === '' || $password_confirm === '') {
    $_SESSION['rp_error'] = 'Todos los campos son obligatorios.';
    header('Location: ../ResetPassword.php?token=' . urlencode($token));
    exit;
}

if ($password !== $password_confirm) {
    $_SESSION['rp_error'] = 'Las contraseñas no coinciden.';
    header('Location: ../ResetPassword.php?token=' . urlencode($token));
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT id, password_reset_expires FROM usuarios WHERE password_reset_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['rp_error'] = 'Token inválido o expirado.';
        header('Location: ../ResetPassword.php?token=' . urlencode($token));
        exit;
    }

    $expires = $user['password_reset_expires'];
    if ($expires === null || new DateTime($expires) < new DateTime()) {
        $_SESSION['rp_error'] = 'Token expirado.';
        header('Location: ../ResetPassword.php?token=' . urlencode($token));
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $upd = $pdo->prepare("UPDATE usuarios SET password = ?, password_reset_token = NULL, password_reset_expires = NULL WHERE id = ?");
    $upd->execute([$hash, $user['id']]);

    // Éxito
    $_SESSION['mensaje'] = 'Contraseña restablecida correctamente. Ahora inicia sesión.';
    header('Location: ../Login.php');
    exit;

} catch (Exception $e) {
    $_SESSION['rp_error'] = 'Error interno.';
    header('Location: ../ResetPassword.php?token=' . urlencode($token));
    exit;
}
