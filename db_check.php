<?php
// PHP/db_check.php - script de diagnóstico rápido (acceder desde /PHP/db_check.php)
require_once 'Base de datos conexion.php';
try {
    $pdo = getPDO();
    echo "Conexión a la base de datos OK.<br>";

    // Comprobar columnas en tabla usuarios
    $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'usuarios'");
    $stmt->execute();
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tabla 'usuarios' columnas: <pre>".htmlspecialchars(implode(', ', $cols))."</pre>";

    // Comprobar existencia tabla asistencia
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'asistencia'");
    $stmt->execute();
    $exists = (int)$stmt->fetchColumn();
    echo "Tabla 'asistencia' existe: " . ($exists ? 'Sí' : 'No') . "<br>";

} catch (Exception $e) {
    echo "Error de conexión: " . htmlspecialchars($e->getMessage());
}
?>