<?php
// rutas_api.php - Maneja CRUD para la tabla 'rutas'
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once 'PHP/Base de datos conexion.php'; 

function sendError($msg, $code = 500) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'msg' => $msg]);
    exit;
}

// 1. Restricción solo a administradores para gestión de rutas
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    sendError('Acceso no autorizado. Solo administradores pueden gestionar rutas.', 403);
}

try {
    $pdo = getPDO();
    $method = $_SERVER['REQUEST_METHOD'];
    $id = intval($_GET['id'] ?? 0); 

    // Obtener datos de entrada (para POST/PUT)
    if ($method === 'POST' || $method === 'PUT') {
        $data = json_decode(file_get_contents("php://input"), true);
        $nombre = trim($data['nombre'] ?? '');
        $conductor = trim($data['conductor'] ?? '');
        $paradas = intval($data['paradas'] ?? 0);

        if ($nombre === '' || $conductor === '' || $paradas <= 0) {
            sendError('Campos incompletos o inválidos.', 400);
        }
    }

    // --- Lógica del CRUD ---

    if ($method === 'GET') {
        // LISTAR TODAS LAS RUTAS
        $stmt = $pdo->query("SELECT id, nombre, conductor, paradas FROM rutas ORDER BY nombre ASC");
        $rutas = $stmt->fetchAll();
        echo json_encode(['ok' => true, 'data' => $rutas]);

    } elseif ($method === 'POST') {
        // CREAR NUEVA RUTA
        $stmt = $pdo->prepare("INSERT INTO rutas (nombre, conductor, paradas) VALUES (:nombre, :conductor, :paradas)");
        $stmt->execute([':nombre' => $nombre, ':conductor' => $conductor, ':paradas' => $paradas]);

        echo json_encode(['ok' => true, 'msg' => 'Ruta registrada con éxito', 'id' => $pdo->lastInsertId()]);

    } elseif ($method === 'PUT' && $id > 0) {
        // ACTUALIZAR RUTA
        $stmt = $pdo->prepare("UPDATE rutas SET nombre = :nombre, conductor = :conductor, paradas = :paradas WHERE id = :id");
        $stmt->execute([':nombre' => $nombre, ':conductor' => $conductor, ':paradas' => $paradas, ':id' => $id]);

        echo json_encode(['ok' => true, 'msg' => 'Ruta actualizada con éxito']);

    } elseif ($method === 'DELETE' && $id > 0) {
        // ELIMINAR RUTA
        $stmt = $pdo->prepare("DELETE FROM rutas WHERE id = :id");
        $stmt->execute([':id' => $id]);

        echo json_encode(['ok' => true, 'msg' => 'Ruta eliminada con éxito']);

    } else {
        sendError('Solicitud inválida. Verifica el método y el ID.', 400);
    }

} catch (\PDOException $e) {
    if ($e->getCode() == 23000) { // Código de error para duplicado (Unique Key)
        sendError('Error: Ya existe una ruta con ese nombre.', 409);
    }
    sendError('Error de base de datos.', 500);
} catch (\Exception $e) {
    sendError('Error interno del servidor.', 500);
}