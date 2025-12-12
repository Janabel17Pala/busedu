<?php
// listar_ruta.php
// Script para obtener la lista de rutas desde la base de datos.
require_once 'Base de datos conexion.php';



// DATOS DE SIMULACIÓN (MOCK)
$rutas = [
    ['id' => 1, 'nombre' => 'Ruta Centro', 'conductor' => 'Juan Pérez', 'paradas' => 8],
    ['id' => 2, 'nombre' => 'Ruta Norte', 'conductor' => 'Ana López', 'paradas' => 12],
    ['id' => 3, 'nombre' => 'Ruta Sur', 'conductor' => 'Pedro Gómez', 'paradas' => 5],
];

if (!empty($_GET['format']) && $_GET['format'] === 'html') {
    if (empty($rutas)) {
        echo '<p class="text-center p-8 bg-white border-2 border-dashed border-gray-200 rounded-lg text-gray-500 italic">No hay rutas registradas aún.</p>';
        exit;
    }
    foreach ($rutas as $r) {
        echo '<div class="bg-white p-4 rounded shadow flex justify-between items-center transition duration-150 hover:shadow-md cursor-pointer mb-2" data-id="' . htmlspecialchars($r['id']) . '">';
        echo '<div><strong>' . htmlspecialchars($r['nombre']) . '</strong><div class="text-sm text-gray-500">Conductor: ' . htmlspecialchars($r['conductor']) . '</div></div>';
        echo '<div class="text-sm text-purple-600 font-semibold">' . htmlspecialchars($r['paradas']) . ' Paradas</div>';
       
        echo '</div>';
    }
    echo '<p class="mt-4 text-sm text-gray-500 italic">NOTA: Estos datos son de **prueba**. Implementa la conexión a la base de datos en `listar_ruta.php`.</p>';
    exit;
}

// Por defecto: JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok' => true, 'data' => $rutas]);
?>