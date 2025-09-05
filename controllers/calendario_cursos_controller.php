<?php
// =================================================================
// Controlador para el Calendario de Cursos Programados
// =================================================================

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

require_once 'models/CalendarioCursosModel.php';

$calendarioCursosModel = new CalendarioCursosModel();
$cursos_programados = $calendarioCursosModel->obtenerCursosProgramados();

$calendar_events = [];
$filter_data = [
    'cursos' => [],
    'profesores' => [],
    'ubicaciones' => []
];

foreach ($cursos_programados as $curso) {
    // --- Preparar datos para los filtros ---
    if (!isset($filter_data['cursos'][$curso['id_curso']])) {
        $filter_data['cursos'][$curso['id_curso']] = $curso['curso_nombre'];
    }
    if (!isset($filter_data['profesores'][$curso['profesor_nombre']])) {
        $filter_data['profesores'][$curso['profesor_nombre']] = $curso['profesor_nombre'];
    }
    if (!isset($filter_data['ubicaciones'][$curso['ubicacion']])) {
        $filter_data['ubicaciones'][$curso['ubicacion']] = $curso['ubicacion'];
    }

    // --- Preparar eventos para el calendario ---
    $fecha_inicio = new DateTime($curso['fecha_inicio']);
    $fecha_fin = new DateTime($curso['fecha_fin']);
    $dias_semana = explode(',', $curso['dias_semana']);

    $intervalo = new DateInterval('P1D');
    $periodo = new DatePeriod($fecha_inicio, $intervalo, $fecha_fin->modify('+1 day'));

    foreach ($periodo as $fecha) {
        if (in_array($fecha->format('N'), $dias_semana)) {
            $start_datetime = $fecha->format('Y-m-d') . 'T' . $curso['hora_inicio'];
            $end_datetime = $fecha->format('Y-m-d') . 'T' . $curso['hora_fin'];

            $calendar_events[] = [
                'title' => $curso['curso_nombre'],
                'start' => $start_datetime,
                'end' => $end_datetime,
                'extendedProps' => [
                    'id_curso' => $curso['id_curso'],
                    'id_area' => $curso['id_area'],
                    'id_sub_area' => $curso['id_sub_area'],
                    'nombre_profesor' => $curso['profesor_nombre'],
                    'ubicacion' => $curso['ubicacion'],
                    'vacantes' => $curso['vacantes_disponibles']
                ]
            ];
        }
    }
}

// Ordenar los datos de los filtros alfabéticamente
asort($filter_data['cursos']);
asort($filter_data['profesores']);
asort($filter_data['ubicaciones']);


// Convertir el array de eventos a JSON para pasarlo a JavaScript
$calendar_events_json = json_encode($calendar_events);

// Cargar la vista del calendario
require_once 'views/calendario_cursos/calendario_cursos_view.php';
