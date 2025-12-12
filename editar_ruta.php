<?php
// editar_ruta.php
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
$nombre = trim($_POST['nombre'] ?? '');
$conductor = trim($_POST['conductor'] ?? '');
$paradas = intval($_POST['paradas'] ?? 0);

if (!$id || $nombre === '' || $conductor === '' || $paradas <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Datos incompletos o ID/paradas inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare("UPDATE rutas SET nombre = :nombre, conductor = :conductor, paradas = :paradas WHERE id = :id");
    $stmt->execute([
        ':nombre' => $nombre, 
        ':conductor' => $conductor, 
        ':paradas' => $paradas, 
        ':id' => $id
    ]);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true, 'msg' => 'Ruta actualizada con éxito'], JSON_UNESCAPED_UNICODE);

    exit;
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'msg' => 'Error al actualizar la ruta: ' . $e->getMessage()]);
    exit;
}