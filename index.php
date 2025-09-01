<?php

// =================================================================
// Front Controller Principal
// =================================================================

// Cargar el archivo de configuración principal
require_once 'config/config.php';

// Cargar las clases del núcleo
require_once 'core/Database.php';
require_once 'core/Session.php';
// Autoloader para las demás clases (opcional pero recomendado para el futuro)
// spl_autoload_register(function($className){
//     require_once 'controllers/' . $className . '.php';
// });


// --- Enrutador Básico ---

// Obtener la vista solicitada de la URL, por defecto será 'login'
$view = $_GET['view'] ?? 'login';

// Lista blanca de vistas permitidas para evitar inclusiones de archivos maliciosas
$allowed_views = [
    'login',
    'dashboard',
    'logout',
    // Vistas de Mantenimientos (Configuración)
    'usuarios', 'clientes', 'cursos', 'areas', 'sub_areas', 'profesores',
    // Vistas de Operaciones
    'monitor', 'programar_horarios', 'asistencia_profesor', 'asistencia_cliente', 'calendario',
    // Vistas de Matrícula
    'matriculas', 'matricula_nueva'
];

// Construir la ruta al archivo del controlador
$controller_file = 'controllers/' . $view . '_controller.php';

// Verificar si la vista está permitida y si el archivo del controlador existe
if (in_array($view, $allowed_views) && file_exists($controller_file)) {
    require_once $controller_file;
} else {
    // Si la vista no es válida o no existe, mostrar un error 404
    // O redirigir al dashboard si ya hay una sesión activa
    http_response_code(404);
    echo "<h1>Error 404: Página no encontrada</h1>";
    // Opcionalmente, podríamos tener una vista para el error 404
    // include 'views/error/404.php';
    exit;
}
