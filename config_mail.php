<?php

// Configuración de correo para envío de reset de contraseña.


return [
    
    'use_smtp' => false,

    // Datos SMTP (si use_smtp = true)
    'smtp_host' => 'smtp.example.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls', // 'tls' o 'ssl' o ''
    'smtp_username' => 'tu@correo.com',
    'smtp_password' => 'tu_contraseña',

    // Datos del remitente
    'from_email' => 'no-reply@busedu.local',
    'from_name' => 'BusEdu',

    // Asunto del email de restablecimiento
    'reset_subject' => 'Restablece tu contraseña en BusEdu',
];
