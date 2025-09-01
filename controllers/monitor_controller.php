<?php

// =================================================================
// Controlador para la página de Monitor de Cursos
// =================================================================

require_once 'models/MonitorModel.php';

// --- Verificación de Seguridad ---
// Cualquier usuario logueado puede ver el monitor.
Session::check();
// ---------------------------------

$monitorModel = new MonitorModel();

// --- Obtención de datos para la vista ---
$cursos_disponibles = $monitorModel->obtenerCursosDisponibles();


// --- Cargar la Vista ---
require_once 'views/monitor_view.php';
