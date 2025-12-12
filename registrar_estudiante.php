<?php
// registrar_estudiante.php
require_once 'Base de datos conexion.php';
session_start();

// Solo vía POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

// Verificar sesión y rol (solo admin puede registrar estudiantes)
if (empty($_SESSION['logged_in']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Acceso no autorizado']);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$parada = trim($_POST['parada'] ?? '');

if ($nombre === '' || $parada === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Campos incompletos']);
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare("INSERT INTO estudiantes (nombre, parada, fecha_registro, hora_registro) VALUES (:nombre, :parada, CURDATE(), CURTIME())");
    $stmt->execute([':nombre' => $nombre, ':parada' => $parada]);

    // Siempre devuelve JSON para AJAX
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true, 'msg' => 'Estudiante registrado', 'id' => $pdo->lastInsertId()]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'msg' => 'Error al guardar el estudiante']);
    exit;
}
