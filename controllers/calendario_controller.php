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

foreach ($cursos_activos as $curso) {
    $fecha_inicio = new DateTime($curso['fecha_inicio']);
    $fecha_fin = new DateTime($curso['fecha_fin']);
    $dias_semana = explode(',', $curso['dias_semana']); // ej: [1, 3, 5]

    // Iterar por cada día entre la fecha de inicio y fin
    $intervalo = new DateInterval('P1D');
    $periodo = new DatePeriod($fecha_inicio, $intervalo, $fecha_fin->modify('+1 day'));

    foreach ($periodo as $fecha) {
        // 'N' devuelve el día de la semana (1=Lunes, 7=Domingo)
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
                    'ubicacion' => $curso['nombre_area'] . ' - ' . $curso['nombre_sub_area'] . ' ' . $curso['numero_sub_area']
                ]
            ];
        }
    }
}

// Convertir el array de eventos a JSON para pasarlo a JavaScript
$calendar_events_json = json_encode($calendar_events);

// Cargar la vista del calendario
require_once 'views/calendario/calendario_view.php';
