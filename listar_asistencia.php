<?php
// listar_asistencia.php
// Script para obtener el historial de asistencia de la base de datos para el día actual.
require_once 'Base de datos conexion.php';


try {
    $pdo = getPDO();
    // Consulta para obtener la asistencia de HOY. 
    $stmt = $pdo->prepare("
        SELECT 
            a.id, 
            DATE_FORMAT(a.fecha, '%d/%m/%Y') as fecha, 
            TIME_FORMAT(a.hora, '%H:%i:%s') as hora,
            a.estado, 
            e.nombre AS estudiante_nombre, 
            e.parada
        FROM 
            asistencia a
        JOIN 
            estudiantes e ON a.estudiante_id = e.id
        WHERE 
            a.fecha = CURDATE() 
        ORDER BY 
            e.nombre ASC
    ");
    $stmt->execute();
    $asistencia_hoy = $stmt->fetchAll();

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true, 'data' => $asistencia_hoy]);
    
} catch (PDOException $e) {
    // Implementación real
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Error de base de datos al listar asistencia']);
    exit;
}
?>