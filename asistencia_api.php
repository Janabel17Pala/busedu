<?php
// asistencia_api.php - Maneja la asistencia de estudiantes (POST y GET para listar)
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once 'PHP/Base de datos conexion.php'; 

function sendError($msg, $code = 500) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'msg' => $msg]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    sendError('Acceso no autorizado.', 403);
}

try {
    $pdo = getPDO();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        // REGISTRAR/ACTUALIZAR ASISTENCIA
        $data = json_decode(file_get_contents("php://input"), true);
        $estudiante_id = intval($data['estudiante_id'] ?? 0);
        $estado = trim($data['estado'] ?? ''); // 'presente' o 'ausente'
        $fecha = trim($data['fecha'] ?? date('Y-m-d')); // Por defecto, la fecha de hoy

        if ($estudiante_id <= 0 || !in_array($estado, ['presente', 'ausente'], true)) {
            sendError('Datos de asistencia inválidos.', 400);
        }

        // Buscar si ya existe un registro para ese estudiante en esa fecha
        $check = $pdo->prepare("SELECT id FROM asistencia WHERE estudiante_id = :eid AND fecha = :fecha");
        $check->execute([':eid' => $estudiante_id, ':fecha' => $fecha]);
        $existing_id = $check->fetchColumn();

        if ($existing_id) {
            // Actualizar registro existente
            $upd = $pdo->prepare("UPDATE asistencia SET estado = :estado WHERE id = :id");
            $upd->execute([':estado' => $estado, ':id' => $existing_id]);
            echo json_encode(['ok' => true, 'msg' => 'Asistencia actualizada', 'id' => $existing_id]);
        } else {
            // Insertar nuevo registro
            $ins = $pdo->prepare("INSERT INTO asistencia (estudiante_id, fecha, estado) VALUES (:eid, :fecha, :estado)");
            $ins->execute([':eid' => $estudiante_id, ':fecha' => $fecha, ':estado' => $estado]);
            echo json_encode(['ok' => true, 'msg' => 'Asistencia registrada', 'id' => $pdo->lastInsertId()]);
        }
        
    } elseif ($method === 'GET') {
        // LISTAR ASISTENCIA (EJEMPLO: Hoy o por fecha)
        $fecha = trim($_GET['fecha'] ?? date('Y-m-d'));
        
        $sql = "
            SELECT 
                a.id, 
                a.estado, 
                a.fecha, 
                e.nombre AS estudiante_nombre,
                e.parada AS estudiante_parada
            FROM asistencia a
            JOIN estudiantes e ON a.estudiante_id = e.id
            WHERE a.fecha = :fecha
            ORDER BY e.nombre ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':fecha' => $fecha]);
        $asistencia = $stmt->fetchAll();

        echo json_encode(['ok' => true, 'data' => $asistencia, 'fecha' => $fecha]);

    } else {
        sendError('Método no permitido.', 405);
    }

} catch (\PDOException $e) {
    sendError('Error de base de datos.', 500);
} catch (\Exception $e) {
    sendError('Error interno del servidor.', 500);
}