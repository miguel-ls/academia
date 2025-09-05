<?php
// =================================================================
// Controlador para el Calendario de Clases
// =================================================================

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

require_once 'models/CalendarioModel.php';

$calendarioModel = new CalendarioModel();
$cursos_activos = $calendarioModel->obtenerCursosActivos();

$calendar_events = [];
$filter_data = [
    'clientes' => [],
    'cursos' => [],
    'profesores' => [],
    'ubicaciones' => []
];

foreach ($cursos_activos as $curso) {
    // --- Preparar datos para los filtros ---
    // Usar IDs como keys para asegurar unicidad
    if (!isset($filter_data['clientes'][$curso['id_cliente']])) {
        $filter_data['clientes'][$curso['id_cliente']] = $curso['nombre_cliente'];
    }
    if (!isset($filter_data['cursos'][$curso['id_curso']])) {
        $filter_data['cursos'][$curso['id_curso']] = $curso['nombre_curso'];
    }
    // Usar el propio nombre como key para asegurar unicidad en strings
    $ubicacion = $curso['nombre_area'] . ' - ' . $curso['nombre_sub_area'] . ' ' . $curso['numero_sub_area'];
    if (!isset($filter_data['profesores'][$curso['nombre_profesor']])) {
        $filter_data['profesores'][$curso['nombre_profesor']] = $curso['nombre_profesor'];
    }
    if (!isset($filter_data['ubicaciones'][$ubicacion])) {
        $filter_data['ubicaciones'][$ubicacion] = $ubicacion;
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
                'title' => $curso['nombre_curso'],
                'start' => $start_datetime,
                'end' => $end_datetime,
                'extendedProps' => [
                    'id_curso' => $curso['id_curso'],
                    'id_area' => $curso['id_area'],
                    'id_sub_area' => $curso['id_sub_area'],
                    'id_cliente' => $curso['id_cliente'],
                    'nombre_cliente' => $curso['nombre_cliente'],
                    'nombre_profesor' => $curso['nombre_profesor'],
                    'ubicacion' => $ubicacion
                ]
            ];
        }
    }
}

// Ordenar los datos de los filtros alfabéticamente
asort($filter_data['clientes']);
asort($filter_data['cursos']);
asort($filter_data['profesores']);
asort($filter_data['ubicaciones']);


// Convertir el array de eventos a JSON para pasarlo a JavaScript
$calendar_events_json = json_encode($calendar_events);

// Cargar la vista del calendario
require_once 'views/calendario/calendario_view.php';
