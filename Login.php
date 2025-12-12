<?php 
// Login.php - Este archivo  maneja la vista y muestra los mensajes de error
session_start();
$message = $_SESSION['mensaje'] ?? null;
unset($_SESSION['mensaje']); // Limpiar el mensaje después de mostrarlo
$error = $_GET['error'] ?? null; // Mensajes de error antiguos (vacío, no encontrado, etc.)
$registro = $_GET['registro'] ?? null; // Mensaje de registro exitoso
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceder - BusEdu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-primary: #5B21B6; /* Morado Oscuro (Indigo 700) */
            --color-accent: #A78BFA; /* Morado Claro (Violet 400) */
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb; 
        }
        .logo-text {
            color: var(--color-primary);
            font-weight: 800; 
            font-size: 2rem; 
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #ede9fe 0%, #f9fafb 100%);
        }
        .btn-login {
            background-color: var(--color-primary);
        }
        .btn-login:hover {
            background-color: #4c1d95; /* Indigo 800 */
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4Cg+b7W9o3F/Xl/0+w8A4E1B3x0vRk8G3s1lVz8w2Pj4yW5g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="login-container">
        <div class="w-full max-w-md bg-white p-8 md:p-10 rounded-xl shadow-2xl border-t-8 border-purple-600 transform transition-all duration-300 hover:shadow-3xl">
            
            <div class="text-center mb-8">
                <h1 class="logo-text mb-2">BusEdu</h1>
                <p class="text-xl font-semibold text-gray-700">Acceso de Usuarios</p>
            </div>

            <?php if ($message): ?>
                <div id="php-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($registro === 'ok'): ?>
                <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">Registro completado. Ahora puedes iniciar sesión.</span>
                </div>
            <?php endif; ?>
            
            <form id="Login-form" action="PHP/login_process.php" method="POST" class="space-y-6">
                
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Usuario (Email o Nombre)</label>
                    <input type="text" id="username" name="username" placeholder="Tu nombre de usuario o email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 transition duration-150 shadow-sm" value=""> </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Contraseña segura" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 transition duration-150 shadow-sm" value=""> <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-purple-600 focus:outline-none">
                            <i id="eye-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-end text-sm">
                    <a href="Forgot.php" class="font-medium text-purple-600 hover:text-purple-500 transition duration-150">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-lg font-bold text-white btn-login hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-[1.01]">
                    Acceder
                </button>
                
            </form>
            
            <p class="mt-6 text-center text-sm text-gray-600">
                ¿Quieres Registrarte como Usuario? 
                <a href="Registro.php" class="font-medium text-purple-600 hover:text-purple-500 transition duration-150">
                    Clic aquí
                </a>
            </p>

        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const togglePasswordButton = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eye-icon');
            const loginForm = document.getElementById('Login-form');
            
            // --- Funcionalidad Mostrar/Ocultar Contraseña ---
            togglePasswordButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
            
            // --- Manejo del Submit del Formulario ---
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;
                
                try {
                    const response = await fetch('PHP/login_process.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            username: username,
                            password: password
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Guardar en localStorage para uso en el cliente
                        localStorage.setItem('busEduUser', data.nombre);
                        localStorage.setItem('userRole', data.rol);
                        localStorage.setItem('isLoggedIn', 'true');
                        
                        // Redirigir a index.html
                        window.location.href = 'index.html';
                    } else {
                        // Mostrar mensaje de error
                        const messageDiv = document.getElementById('php-message') || document.createElement('div');
                        messageDiv.id = 'php-message';
                        messageDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 role="alert"';
                        messageDiv.innerHTML = `<strong class="font-bold">Error:</strong><span class="block sm:inline">${data.message}</span>`;
                        loginForm.parentElement.insertBefore(messageDiv, loginForm);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error en la conexión. Intenta nuevamente.');
                }
            });

        });
    </script>
        <script src="assets/js/userBadge.js"></script>
</html>