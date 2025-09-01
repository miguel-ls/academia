<?php

// =================================================================
// Archivo de Configuración del Sistema
// =================================================================

// Configuración de la Base de Datos
define('DB_HOST', '127.0.0.1'); // O la IP de tu servidor de BD
define('DB_USER', 'miguel');
define('DB_PASS', 'Miguel123!'); // Colocar la contraseña si es necesaria
define('DB_NAME', 'academia_cursos'); // Nombre de la BD

// Configuración del Sitio
define('SITE_URL', 'http://localhost/academia'); // URL base de tu sitio
define('SITE_NAME', 'Academia de Cursos');

// Configuración para el envío de correos (para 2FA y notificaciones)
define('MAIL_HOST', 'mail.codesicorp.com');
define('MAIL_USER', 'ventas.sistemas@codesicorp.com');
define('MAIL_PASS', 'Mcausit@.977611');
define('MAIL_PORT', 465); // o 465
define('SMTP_FROM_EMAIL', 'ventas.sistemas@codesicorp.com');
define('SMTP_FROM_NAME', 'Academia de Cursos');

// Otras configuraciones
session_start();
date_default_timezone_set('America/Lima'); // Ajusta a tu zona horaria
