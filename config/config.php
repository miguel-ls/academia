<?php

// =================================================================
// Archivo de Configuración del Sistema
// =================================================================

// Configuración de la Base de Datos
define('DB_HOST', 'localhost'); // O la IP de tu servidor de BD
define('DB_USER', 'root');
define('DB_PASS', '1q2w3e4r5t.'); // Colocar la contraseña si es necesaria
define('DB_NAME', 'academia_cursos'); // Nombre de la BD

// Configuración del Sitio
define('SITE_URL', 'http://localhost/academia/'); //(con la barra al final).
define('SITE_NAME', 'ERPPLUS - Academy');
define('CORP_NAME', 'CODESICORP S.A.C.');

// Configuración para el envío de correos (para 2FA y notificaciones)
define('SMTP_HOST', 'mail.codesicorp.com');
define('SMTP_USER', 'ventas.sistemas@codesicorp.com');
define('SMTP_PASS', 'Mcausit@.977611');
define('SMTP_PORT', 465); // O 465 para SSL
define('SMTP_FROM_EMAIL', 'ventas.sistemas@codesicorp.com');
define('SMTP_FROM_NAME', 'ERPPLUS - Academy');

define('URL_NODE_RED', 'http://localhost:1880');
define('TOKEN_NODE_RED', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c');

define('EMP_CCODIGO', '087');

// Otras configuraciones
session_start();
date_default_timezone_set('America/Lima'); // Ajusta a tu zona horaria
