<?php
session_start();

// Si no ha iniciado sesión → fuera
if (!isset($_SESSION["user_name"]) || $_SESSION["user_role"] !== "user") {
    header("Location: Login.php");
    exit;
}

$nombre = $_SESSION["user_name"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - BusEdu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-xl mx-auto bg-white shadow-lg p-6 rounded-xl">
        <h1 class="text-3xl font-bold text-purple-700">Bienvenido, <?php echo htmlspecialchars($nombre); ?> 👋</h1>

        <p class="mt-4 text-gray-700">
            Este es tu panel de usuario.  
            Aquí solo puedes visualizar tu información.  
            No puedes editar, eliminar ni agregar datos.
        </p>

        <div class="mt-6">
            <a href="logout.php" 
               class="px-4 py-2 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700">Cerrar sesión</a>
        </div>
    </div>
</body>
</html>
