<?php
// Base de datos conexion.php - Conexión Principal para MySQL

declare(strict_types=1);


$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('DB_NAME') ?: 'busedu_mysql';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_PORT = getenv('DB_PORT') ?: '3306';

$pdo = null;

// -----------------------------------------------------------------
// 2. CONEXIÓN (Manejo Robusto de Errores)
// -----------------------------------------------------------------
try {
    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);

   

} catch (\PDOException $e) {
    // Mensaje claro para desarrollo. En producción considera registrar el error en un log.
    http_response_code(500);
    echo "<h1> Error de Conexión a la Base de Datos</h1>";
    echo "<p><strong>Comprueba que MySQL esté ejecutándose y que las credenciales en <code>PHP/Base de datos conexion.php</code> sean correctas.</strong></p>";
    echo "<ul>";
    echo "<li>Host: " . htmlspecialchars($DB_HOST) . "</li>";
    echo "<li>Puerto: " . htmlspecialchars($DB_PORT) . "</li>";
    echo "<li>Base de datos: " . htmlspecialchars($DB_NAME) . "</li>";
    echo "<li>Usuario: " . htmlspecialchars($DB_USER) . "</li>";
    echo "</ul>";
    echo "<p>Mensaje de PDO: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}


/**
 * Devuelve la instancia PDO (conexión a la BD).
 */
function getPDO(): PDO {
    global $pdo;
    if ($pdo === null) {
        throw new \Exception("PDO no está inicializado. Error de conexión previo.");
    }
    return $pdo;
}

/**
 * Función que asegura la existencia de un usuario Administrador por defecto.
 * Contraseña: '123456' (solo para entornos locales de prueba)
 */
function ensureDefaultAdmin(): void {
    try {
        $pdo = getPDO();

        $stmt = $pdo->query("SELECT COUNT(*) AS c FROM usuarios");
        $row = $stmt->fetch();

        if ($row && $row['c'] == 0) {
            $hash = password_hash('123456', PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO usuarios (nombre, username, password, rol) VALUES (?, ?, ?, ?)");
            $ins->execute(['Administrador BusEdu', 'admin@busedu.com', $hash, 'admin']);
        }
    } catch (\PDOException $e) {
      
    }
}


function testConnection(): array {
    try {
        $pdo = getPDO();
        $stmt = $pdo->query('SELECT 1');
        $stmt->fetch();
        return ['ok' => true];
    } catch (\Exception $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}