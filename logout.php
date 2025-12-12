<?php
// logout.php - destruye la sesión en el servidor y redirige al login
session_start();
// Limpiar todas las variables de sesión
$_SESSION = [];
// Destruir la sesión y la cookie asociada
if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]
	);
}
session_unset();
session_destroy();

// Redirigir al index (página principal) en lugar del login
header("Location: ../index.html");
exit;
?>
