<?php

// =================================================================
// Controlador para la gestión de Matrículas
// =================================================================

require_once 'models/MatriculaModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$matriculaModel = new MatriculaModel();

// Determinar la acción: mostrar la lista o el formulario de nueva matrícula
$action = $_GET['action'] ?? 'list';

// Cargar modelos adicionales necesarios para la página de nueva matrícula
require_once 'models/ClienteModel.php';
require_once 'models/MonitorModel.php';
$clienteModel = new ClienteModel();
$monitorModel = new MonitorModel();


switch ($action) {
    case 'list':
        $matriculas = $matriculaModel->obtenerTodas();
        require_once 'views/matriculas_view.php';
        break;

    case 'nueva':
        // Cargar la vista principal del formulario
        require_once 'views/matricula_nueva_view.php';
        break;

    case 'buscar_cliente':
        // Endpoint para AJAX
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';
        $clientes = $clienteModel->buscar($query);
        echo json_encode($clientes);
        exit;

    case 'buscar_cursos':
        // Endpoint para AJAX
        header('Content-Type: application/json');
        // Aquí se usarían los filtros, por ahora usamos el método general
        $cursos = $monitorModel->obtenerCursosDisponibles();
        echo json_encode($cursos);
        exit;

    case 'registrar_matricula':
        // Endpoint para el POST final del formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Recalcular totales en el backend por seguridad
                $monto_total = 0;
                $descuento_total = 0;
                $cursos_detalle = [];

                if(empty($_POST['cursos'])){
                     throw new Exception("Debe seleccionar al menos un curso.");
                }

                foreach ($_POST['cursos'] as $id_curso_programado => $curso) {
                    $monto_total += (float)$curso['precio_pactado'];
                    $descuento_total += (float)$curso['descuento'];
                    $cursos_detalle[] = [
                        'id_curso_programado' => (int)$id_curso_programado,
                        'precio_pactado' => (float)$curso['precio_pactado'],
                        'descuento' => (float)$curso['descuento']
                    ];
                }
                $monto_final = $monto_total - $descuento_total;

                $datos_matricula = [
                    'id_cliente' => (int)$_POST['id_cliente'],
                    'id_forma_pago' => (int)$_POST['id_forma_pago'],
                    'fecha_inicio_matricula' => $_POST['fecha_inicio_matricula'],
                    'fecha_fin_matricula' => $_POST['fecha_fin_matricula'],
                    'observaciones' => $_POST['observaciones'],
                    'monto_total' => $monto_total,
                    'descuento_total' => $descuento_total,
                    'monto_final' => $monto_final,
                    'cursos' => $cursos_detalle
                ];

                $matriculaModel->registrarMatricula($datos_matricula);

                // Redirigir a la lista con un mensaje de éxito
                header('Location: index.php?view=matriculas&success=1');
                exit;

            } catch (Exception $e) {
                // Redirigir de vuelta al formulario con un mensaje de error
                header('Location: index.php?view=matriculas&action=nueva&error=' . urlencode($e->getMessage()));
                exit;
            }
        }
        break;

    case 'anular':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_matricula = (int)$_POST['id_matricula'];
                $observaciones = $_POST['observaciones'];
                if ($id_matricula > 0) {
                    $matriculaModel->anular($id_matricula, $observaciones);
                    header('Location: index.php?view=matriculas&success_anulacion=1');
                    exit;
                }
            } catch (Exception $e) {
                header('Location: index.php?view=matriculas&error_anulacion=' . urlencode($e->getMessage()));
                exit;
            }
        }
        break;

    default:
        $matriculas = $matriculaModel->obtenerTodas();
        require_once 'views/matriculas_view.php';
        break;
}
