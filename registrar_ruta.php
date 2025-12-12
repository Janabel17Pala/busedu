<?php
// registrar_ruta.php
require_once 'Base de datos conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

// Verificar sesión y rol (solo admin puede registrar rutas)
if (empty($_SESSION['logged_in']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Acceso no autorizado']);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$conductor = trim($_POST['conductor'] ?? '');
$paradas = intval($_POST['paradas'] ?? 0);

if ($nombre === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'El nombre de la ruta es requerido']);
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare("INSERT INTO rutas (nombre, conductor, paradas, hora_registro) VALUES (:nombre, :conductor, :paradas, CURTIME())");
    $stmt->execute([':nombre' => $nombre, ':conductor' => $conductor, ':paradas' => $paradas]);

    // Siempre devuelve JSON para AJAX
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true, 'msg' => 'Ruta registrada', 'id' => $pdo->lastInsertId()]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'msg' => 'Error al guardar la ruta: ' . $e->getMessage()]);
    exit;
}
?>
