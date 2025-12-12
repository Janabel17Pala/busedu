<?php
session_start();
$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Restablecer Contraseña - BusEdu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-gray-50">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow">
        <h1 class="text-2xl font-bold text-purple-700 mb-4">Restablecer Contraseña</h1>
        <?php if (!empty($_SESSION['rp_error'])): ?>
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($_SESSION['rp_error']); unset($_SESSION['rp_error']); ?></div>
        <?php endif; ?>

        <form action="PHP/process_reset.php" method="POST" class="space-y-4">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nueva contraseña</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                <input type="password" name="password_confirm" required class="w-full px-4 py-2 border rounded">
            </div>
            <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded">Restablecer contraseña</button>
        </form>

        <p class="mt-4 text-sm"><a href="Login.php" class="text-purple-600 font-semibold">Volver al login</a></p>
    </div>
    <script src="assets/js/userBadge.js"></script>
</body>
</html>