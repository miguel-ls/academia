<?php

// =================================================================
// Controlador para el Panel Principal (Dashboard)
// =================================================================

// Primero, verificar que el usuario tenga una sesión activa.
// Si no, la clase Session lo redirigirá al login.
Session::check();

// Aquí iría la lógica para obtener los datos de los gráficos, etc.
// Por ahora, solo cargamos la vista.

$nombre_usuario = $_SESSION['user_fullname'];


// Cargar el modelo del dashboard
require_once 'models/DashboardModel.php';
$dashboardModel = new DashboardModel();

// --- Lógica de Filtros ---
$anio_seleccionado = $_GET['anio'] ?? date('Y');
$mes_seleccionado = $_GET['mes'] ?? date('m');

// --- Obtención de datos para los gráficos ---
$ventas_por_curso_data = $dashboardModel->getVentasPorCurso($anio_seleccionado, $mes_seleccionado);
$ventas_mensuales_data = $dashboardModel->getVentasMensuales($anio_seleccionado);

// Preparar los datos para que JavaScript los pueda usar fácilmente
$chart_data_pie = [
    'labels' => array_column($ventas_por_curso_data, 'nombre_curso'),
    'data' => array_column($ventas_por_curso_data, 'total_ventas')
];

// Para el gráfico de barras, necesitamos asegurarnos de que todos los meses estén presentes
$ventas_barras_raw = array_column($ventas_mensuales_data, 'total_ventas', 'mes');
$chart_data_bar = [
    'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
    'data' => []
];
for ($i = 1; $i <= 12; $i++) {
    $chart_data_bar['data'][] = $ventas_barras_raw[$i] ?? 0;
}


// --- Obtención de datos para el tercer gráfico ---
$ventas_por_curso_area_data = $dashboardModel->getVentasPorCursoArea($anio_seleccionado, $mes_seleccionado);
$chart_data_pie_area = [
    'labels' => array_column($ventas_por_curso_area_data, 'label'),
    'data' => array_column($ventas_por_curso_area_data, 'total_ventas')
];

// Convertir los datos a JSON para inyectarlos en el script de la vista
$json_data_pie = json_encode($chart_data_pie);
$json_data_bar = json_encode($chart_data_bar);
$json_data_pie_area = json_encode($chart_data_pie_area);


// Cargar la vista del dashboard
require_once 'views/dashboard/dashboard_view.php';
