<?php
session_start();
ob_start();

require_once 'PHP/Base de datos conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = 'user'; // Solo usuarios regulares, sin opción admin

    if (!$name || !$username || !$password) {
        $error = "Todos los campos son obligatorios.";
    } else {
        try {
            $pdo = getPDO();
            
            // Verificar si el usuario ya existe
            $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM usuarios WHERE username = ?");
            $stmt->execute([$username]);
            $result = $stmt->fetch();
            
            if ($result['c'] > 0) {
                $error = "Ya existe un usuario con este correo.";
            } else {
                // Hash de la contraseña
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insertar usuario en BD
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, username, password, rol) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $username, $hashedPassword, $role]);
                
                // Redirigir al login con mensaje de éxito
                header("Location: Login.php?registro=ok");
                exit();
            }
        } catch (Exception $e) {
            $error = "Error al registrar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - BusEdu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-primary: #5B21B6;
            --color-secondary: #8B5CF6;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        .logo-text {
            color: var(--color-primary);
            font-weight: 900;
            font-size: 2.25rem;
        }
        .btn-register {
            background-color: var(--color-secondary);
        }
        .btn-register:hover {
            background-color: #7c3aed;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white p-8 md:p-10 rounded-xl shadow-2xl border-t-8 border-purple-600">

        <div class="text-center mb-8">
            <h1 class="logo-text">BusEdu</h1>
            <h2 class="text-2xl font-bold text-gray-800 mt-4">Registro de Nuevo Usuario</h2>
            <p class="text-sm text-gray-500">Crea tu cuenta para acceder a la información de rutas y asistencia.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Correo Electrónico (Usuario)</label>
                <input type="email" name="username" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                <div class="relative">
                    <input type="password" id="reg-password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    <button type="button" id="toggleRegPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-purple-600 focus:outline-none">
                        <i id="reg-eye-icon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>


            <button type="submit" class="w-full px-5 py-3 font-bold text-white rounded-lg btn-register">
                Registrarme
            </button>
        </form>

        <p class="text-sm text-center text-gray-600 mt-6">
            ¿Ya tienes cuenta? <a href="Login.php" class="text-purple-600 font-bold hover:underline">Accede aquí</a>
        </p>
    </div>
    <script src="assets/js/userBadge.js"></script>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('toggleRegPassword');
        const pwdInput = document.getElementById('reg-password');
        const eye = document.getElementById('reg-eye-icon');
        if (toggleBtn && pwdInput) {
            toggleBtn.addEventListener('click', () => {
                const type = pwdInput.getAttribute('type') === 'password' ? 'text' : 'password';
                pwdInput.setAttribute('type', type);
                eye.classList.toggle('fa-eye');
                eye.classList.toggle('fa-eye-slash');
            });
        }
    });
</script>
