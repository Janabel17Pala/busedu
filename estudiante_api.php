<?php
// estudiantes_api.php - Maneja CRUD para la tabla 'estudiantes'
header('Content-Type: application/json; charset=utf-8');
session_start();

// Carga el archivo de conexión (contiene getPDO() y la lógica de error)
require_once 'PHP/Base de datos conexion.php'; 

// Función auxiliar para enviar respuesta de error
function sendError($msg, $code = 500) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'msg' => $msg]);
    exit;
}

// 1. Solo permitir la gestión a usuarios autenticados
if (!isset($_SESSION['user_id'])) {
    sendError('Acceso no autorizado.', 403);
}

try {
    $pdo = getPDO();
    $method = $_SERVER['REQUEST_METHOD'];
    $id = intval($_GET['id'] ?? 0); // ID para operaciones PUT/DELETE

    // === INICIO DEL CAMBIO: Verificar ROL para acciones de modificación ===
    $userRole = $_SESSION['rol'] ?? 'user';
    if (($method === 'POST' || $method === 'PUT' || $method === 'DELETE') && $userRole !== 'admin') {
        sendError('Permiso denegado. Solo administradores pueden realizar esta acción.', 403);
    }
    // === FIN DEL CAMBIO ===

    // Obtener datos de entrada (para POST/PUT)
    if ($method === 'POST' || $method === 'PUT') {
        // Lee el cuerpo de la petición como JSON
        $data = json_decode(file_get_contents("php://input"), true);
        $nombre = trim($data['nombre'] ?? '');
        $parada = trim($data['parada'] ?? '');

        if ($nombre === '' || $parada === '') {
            sendError('Campos incompletos o inválidos.', 400);
        }
    }

    // --- Lógica del CRUD ---

    if ($method === 'GET') {
        // LISTAR ESTUDIANTES (Permitido para todos los logueados)
        $stmt = $pdo->query("SELECT id, nombre, parada, fecha_registro FROM estudiantes ORDER BY nombre ASC");
        $estudiantes = $stmt->fetchAll();
        echo json_encode(['ok' => true, 'data' => $estudiantes]);

    } elseif ($method === 'POST') {
        // REGISTRAR NUEVO ESTUDIANTE (Protegido por el check de rol arriba)
        $stmt = $pdo->prepare("INSERT INTO estudiantes (nombre, parada) VALUES (:nombre, :parada)");
        $stmt->execute([':nombre' => $nombre, ':parada' => $parada]);

        echo json_encode(['ok' => true, 'msg' => 'Estudiante registrado con éxito', 'id' => $pdo->lastInsertId()]);

    } elseif ($method === 'PUT' && $id > 0) {
        // ACTUALIZAR ESTUDIANTE (Protegido por el check de rol arriba)
        $stmt = $pdo->prepare("UPDATE estudiantes SET nombre = :nombre, parada = :parada WHERE id = :id");
        $stmt->execute([':nombre' => $nombre, ':parada' => $parada, ':id' => $id]);

        echo json_encode(['ok' => true, 'msg' => 'Estudiante actualizado con éxito']);

    } elseif ($method === 'DELETE' && $id > 0) {
        // ELIMINAR ESTUDIANTE (Protegido por el check de rol arriba)
        $stmt = $pdo->prepare("DELETE FROM estudiantes WHERE id = :id");
        $stmt->execute([':id' => $id]);

        echo json_encode(['ok' => true, 'msg' => 'Estudiante eliminado con éxito']);

    } else {
        sendError('Solicitud inválida. Verifica el método y los parámetros.', 400);
    }

} catch (\PDOException $e) {
    if ($e->getCode() == 23000) { // Código de error para duplicado (Unique Key)
        sendError('Error: Ya existe un estudiante con ese nombre y parada.', 409);
    }
    sendError('Error de base de datos. ' . $e->getMessage(), 500);
} catch (\Exception $e) {
    sendError('Error interno del servidor.', 500);
}