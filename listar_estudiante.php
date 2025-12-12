<?php
// listar_estudiante.php
require_once 'Base de datos conexion.php';
session_start(); // Iniciar la sesión para acceder al rol
$isAdmin = ($_SESSION['rol'] ?? '') === 'admin'; // Verificar si el rol es 'admin'

try {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT id, nombre, parada, DATE_FORMAT(fecha_registro, '%d/%m/%Y') as fecha, TIME_FORMAT(hora_registro, '%H:%i:%s') as hora FROM estudiantes ORDER BY nombre ASC");
    $estudiantes = $stmt->fetchAll();

    // Si piden HTML (ej: fetch desde Estudiantes.html), devolvemos markup
    if (!empty($_GET['format']) && $_GET['format'] === 'html') {
        if (empty($estudiantes)) {
            echo '<p class="text-center p-8 bg-white border-2 border-dashed border-gray-200 rounded-lg text-gray-500 italic">No hay estudiantes registrados aún.</p>';
            exit;
        }
        foreach ($estudiantes as $e) {
            echo '<div class="bg-white p-4 rounded shadow flex justify-between items-center">';
            
            // Información del estudiante
            echo '<div><strong>' . htmlspecialchars($e['nombre']) . '</strong><div class="text-sm text-gray-500">' . htmlspecialchars($e['parada']) . '</div><div class="text-xs text-gray-400">' . htmlspecialchars($e['fecha']) . ' a las ' . htmlspecialchars($e['hora']) . '</div></div>';
            
            // INICIO DE LA RESTRICCIÓN: Solo imprime los botones si es administrador
            if ($isAdmin) {
                // Contenedor de Botones
                echo '<div class="flex space-x-2">';
                
                // Botón Editar 
                echo '<button type="button" data-id="' . htmlspecialchars($e['id']) . '" data-nombre="' . htmlspecialchars($e['nombre']) . '" data-parada="' . htmlspecialchars($e['parada']) . '" class="edit-btn bg-purple-500 hover:bg-purple-600 text-white font-semibold py-1 px-3 rounded text-sm transition duration-150">Editar</button>';
                
                // Botón Eliminar
                echo '<button type="button" data-id="' . htmlspecialchars($e['id']) . '" data-nombre="' . htmlspecialchars($e['nombre']) . '" class="delete-btn bg-red-500 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded text-sm transition duration-150">Eliminar</button>';
                
                echo '</div>'; // Fin Contenedor de Botones
            }
            // FIN DE LA RESTRICCIÓN
            
            echo '</div>';
        }
        exit;
    }

    // Por defecto: JSON (útil para fetch/AJAX)
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true, 'data' => $estudiantes], JSON_UNESCAPED_UNICODE);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Error al obtener estudiantes']);
    exit;
}