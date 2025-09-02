<?php

// =================================================================
// Archivo de Configuración del Sistema
// =================================================================

// Configuración de la Base de Datos
define('DB_HOST', '127.0.0.1'); // O la IP de tu servidor de BD
define('DB_USER', 'root');      // Usuario de la BD
define('DB_PASS', 'password');  // Contraseña de la BD (cámbiala por una segura)
define('DB_NAME', 'academia_cursos'); // Nombre de la BD

// Configuración del Sitio
define('SITE_URL', 'http://localhost/academia/'); // URL base de tu sitio (¡con barra al final!)
define('SITE_NAME', 'Academia de Cursos');

// Configuración para el envío de correos (para 2FA y notificaciones)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_USER', 'user@example.com');
define('SMTP_PASS', 'your_smtp_password');
define('SMTP_PORT', 587); // O 465 para SSL
define('SMTP_FROM_EMAIL', 'no-reply@example.com');
define('SMTP_FROM_NAME', 'Academia de Cursos');

// Otras configuraciones
session_start();
date_default_timezone_set('America/Lima'); // Ajusta a tu zona horaria
