<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Recuperar Contraseña - BusEdu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-gray-50">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow">
        <h1 class="text-2xl font-bold text-purple-700 mb-4">Recuperar Contraseña</h1>
        <p class="text-sm text-gray-600 mb-4">Introduce tu correo registrado y te daremos instrucciones para restablecer la contraseña.</p>

        <?php if (!empty($_SESSION['fp_message'])): ?>
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($_SESSION['fp_message']); unset($_SESSION['fp_message']); ?></div>
        <?php endif; ?>

        <form action="PHP/send_reset.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded">
            </div>
            <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded">Enviar instrucciones</button>
        </form>

        <p class="mt-4 text-sm text-gray-600">Recibirás un enlace de restablecimiento (en desarrollo se mostrará directamente).</p>
        <p class="mt-4 text-sm"><a href="Login.php" class="text-purple-600 font-semibold">Volver al login</a></p>
    </div>
        <script src="assets/js/userBadge.js"></script>
    </body>
    </html>