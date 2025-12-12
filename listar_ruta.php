<?php
// listar_ruta.php
// Script para listar todas las rutas de la base de datos
require_once 'Base de datos conexion.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = getPDO();
    // Agregamos created_at y hora_registro para mantener la información de la fila
    $stmt = $pdo->query("SELECT id, nombre, conductor, paradas, DATE_FORMAT(created_at, '%d/%m/%Y') as fecha, TIME_FORMAT(hora_registro, '%H:%i:%s') as hora FROM rutas ORDER BY id DESC");
    $rutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si es petición AJAX para HTML
    if (isset($_GET['format']) && $_GET['format'] === 'html') {
        header('Content-Type: text/html; charset=utf-8');
        
        if (empty($rutas)) {
            echo '<p class="text-center text-gray-400 italic">No hay rutas registradas aún. ¡Usa el formulario para empezar!</p>';
            exit;
        }
        
        // Verificar si es admin para mostrar botones
        $isAdmin = !empty($_SESSION['logged_in']) && ($_SESSION['rol'] ?? '') === 'admin';

        foreach ($rutas as $ruta) {
            $id = htmlspecialchars($ruta['id']);
            $nombre = htmlspecialchars($ruta['nombre']);
            $conductor = htmlspecialchars($ruta['conductor'] ?? 'N/A');
            $paradas = htmlspecialchars($ruta['paradas'] ?? 0);
            $fecha = htmlspecialchars($ruta['fecha']);
            $hora = htmlspecialchars($ruta['hora']);
            
            echo '<div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-purple-500 mb-3">';
            echo '<div class="flex justify-between items-start">';
            echo '<div>';
            echo '<h3 class="font-bold text-gray-800">' . $nombre . '</h3>';
            echo '<p class="text-sm text-gray-600">Conductor: ' . $conductor . '</p>';
            echo '<p class="text-sm text-gray-600">Paradas: ' . $paradas . '</p>';
            echo '<p class="text-xs text-gray-400">' . $fecha . ' a las ' . $hora . '</p>';
            echo '</div>';
            
            // Botones de acción solo para Admin
            if ($isAdmin) {
                echo '<div class="flex space-x-2">';
                // Botón Editar 
                echo '<button type="button" data-id="' . $id . '" data-nombre="' . $nombre . '" data-conductor="' . $conductor . '" data-paradas="' . $paradas . '" class="edit-btn bg-purple-500 hover:bg-purple-600 text-white font-semibold py-1 px-3 rounded text-sm transition duration-150">Editar</button>';
                // Botón Eliminar 
                echo '<button type="button" data-id="' . $id . '" data-nombre="' . $nombre . '" class="delete-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition duration-150">Eliminar</button>';
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
        }
        exit;
    }

    // Si es petición JSON (por defecto)
    echo json_encode(['ok' => true, 'data' => $rutas]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Error al cargar rutas']);
    exit;
}
?>