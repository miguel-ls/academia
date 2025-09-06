<?php

// =================================================================
// Controlador para el Panel Principal (Dashboard)
// =================================================================

Session::check();

require_once 'models/DashboardModel.php';
$dashboardModel = new DashboardModel();

// --- Lógica de Filtros ---
$anio_seleccionado = $_GET['anio'] ?? date('Y');
$mes_seleccionado = $_GET['mes'] ?? date('m');
$meses_nombres = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

// --- Obtención de datos para los gráficos circulares (sin cambios) ---
$ventas_por_curso_data = $dashboardModel->getVentasPorCurso($anio_seleccionado, $mes_seleccionado);
$ventas_por_curso_area_data = $dashboardModel->getVentasPorCursoArea($anio_seleccionado, $mes_seleccionado);

$chart_data_pie = [
    'labels' => array_column($ventas_por_curso_data, 'nombre_curso'),
    'data' => array_column($ventas_por_curso_data, 'total_ventas')
];
$chart_data_pie_area = [
    'labels' => array_column($ventas_por_curso_area_data, 'label'),
    'data' => array_column($ventas_por_curso_area_data, 'total_ventas')
];

// --- Obtención y procesamiento de datos para el gráfico de barras apiladas ---
$ventas_anuales_raw = $dashboardModel->getVentasAnualesPorCurso($anio_seleccionado);

// 1. Agrupar datos por curso
$datos_por_curso = [];
foreach ($ventas_anuales_raw as $fila) {
    $datos_por_curso[$fila['nombre_curso']][$fila['mes']] = $fila['total_ventas'];
}

// 2. Construir la estructura de datasets para Chart.js
$datasets_barras = [];
$colores_cursos = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#6610f2', '#e83e8c', '#17a2b8'];
$color_index = 0;

foreach ($datos_por_curso as $curso => $ventas_mes) {
    $datos_mes_completo = [];
    for ($i = 1; $i <= 12; $i++) {
        $datos_mes_completo[] = $ventas_mes[$i] ?? 0;
    }

    $datasets_barras[] = [
        'label' => $curso,
        'data' => $datos_mes_completo,
        'backgroundColor' => $colores_cursos[$color_index % count($colores_cursos)]
    ];
    $color_index++;
}

// 3. Estructura final para el gráfico de barras
$chart_data_bar = [
    'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
    'datasets' => $datasets_barras
];


// --- Convertir todos los datos a JSON para la vista ---
$json_data_pie = json_encode($chart_data_pie);
$json_data_pie_area = json_encode($chart_data_pie_area);
$json_data_bar = json_encode($chart_data_bar);


// Cargar la vista del dashboard
require_once 'views/dashboard/dashboard_view.php';
