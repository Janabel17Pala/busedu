<?php
/**
 * login_process.php - Procesa el login y crea la sesión
 */
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    // Importar conexión a BD
    require_once 'Base de datos conexion.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son requeridos']);
        exit;
    }
    
    // Buscar usuario en BD
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ? OR nombre = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }
    
    // Verificar contraseña
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
        exit;
    }
    
    // Crear sesión
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nombre'] = $user['nombre'];
    $_SESSION['rol'] = $user['rol'];
    $_SESSION['logged_in'] = true;
    
    // Responder con éxito
    echo json_encode([
        'success' => true,
        'message' => 'Login exitoso',
        'redirect' => 'index.html',
        'nombre' => $user['nombre'],
        'rol' => $user['rol']
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
