<?php
/**
 * check_session.php - Verifica el estado de la sesión
 */
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo json_encode([
        'logged_in' => true,
        'nombre' => $_SESSION['nombre'] ?? 'Usuario',
        'rol' => $_SESSION['rol'] ?? 'user'
    ]);
} else {
    echo json_encode([
        'logged_in' => false
    ]);
}
?>
