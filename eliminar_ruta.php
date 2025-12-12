<?php
// eliminar_ruta.php
require_once 'Base de datos conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

// Verificar sesión y rol (Requiere ser admin)
if (empty($_SESSION['logged_in']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Acceso no autorizado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'ID de ruta inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare("DELETE FROM rutas WHERE id = :id");
    $stmt->execute([':id' => $id]);

    header('Content-Type: application/json; charset=utf-8');
    if ($stmt->rowCount() > 0) {
        echo json_encode(['ok' => true, 'msg' => 'Ruta eliminada con éxito'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['ok' => false, 'msg' => 'Ruta no encontrada o ya eliminada'], JSON_UNESCAPED_UNICODE);
    }
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'msg' => 'Error al eliminar la ruta.'], JSON_UNESCAPED_UNICODE);
    exit;
}