<?php


$sqlFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'db_init.sql';
if (!file_exists($sqlFile)) {
    echo "No se encontró db_init.sql en la raíz del proyecto.";
    exit;
}

require_once 'Base de datos conexion.php';

try {
    $sql = file_get_contents($sqlFile);
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    // Ejecutar múltiples sentencias: separar por ; y ejecutar secuencialmente
    $stmts = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($stmts as $stmt) {
        if ($stmt === '') continue;
        $pdo->exec($stmt);
    }
    echo "Base de datos y tablas creadas/actualizadas correctamente. Revisa phpMyAdmin para confirmarlo.";
} catch (PDOException $e) {
    echo "Error al inicializar la base de datos: " . htmlspecialchars($e->getMessage());
}

?>
