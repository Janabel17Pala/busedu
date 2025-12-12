<?php
// registrar_asistencia.php

require_once 'Base de datos conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

// Solo administradores pueden registrar o modificar asistencias
if (empty($_SESSION['logged_in']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Acceso no autorizado']);
    exit;
}

$estudiante_id = intval($_POST['estudiante_id'] ?? 0);
$estado = trim($_POST['estado'] ?? '');
$fecha = trim($_POST['fecha'] ?? date('Y-m-d')); // permite enviar fecha; si no, hoy

if ($estudiante_id <= 0 || !in_array($estado, ['presente', 'ausente'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
    exit;
}

try {
    $pdo = getPDO();

    // evitar duplicados para mismo estudiante y fecha
    $check = $pdo->prepare("SELECT id FROM asistencia WHERE estudiante_id = :eid AND fecha = :fecha");
    $check->execute([':eid' => $estudiante_id, ':fecha' => $fecha]);
    if ($check->fetch()) {
        // Actualizar registro existente 
        $upd = $pdo->prepare("UPDATE asistencia SET estado = :estado, hora = CURTIME() WHERE estudiante_id = :eid AND fecha = :fecha");
        $upd->execute([':estado' => $estado, ':eid' => $estudiante_id, ':fecha' => $fecha]);
        echo json_encode(['ok' => true, 'msg' => 'Asistencia actualizada']);
        exit;
    }

    $ins = $pdo->prepare("INSERT INTO asistencia (estudiante_id, fecha, estado, hora) VALUES (:eid, :fecha, :estado, CURTIME())");
    $ins->execute([':eid' => $estudiante_id, ':fecha' => $fecha, ':estado' => $estado]);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true, 'msg' => 'Asistencia registrada', 'id' => $pdo->lastInsertId()]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'msg' => 'Error al registrar asistencia']);
    exit;
}
