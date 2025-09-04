<?php

// =================================================================
// Controlador para la Asistencia de Clientes
// =================================================================

require_once 'models/AsistenciaClienteModel.php';
require_once 'models/ClienteModel.php';
require_once 'models/CursosModel.php';
require_once 'utils/helpers.php';

// --- Verificación de Seguridad ---
Session::check();
if (!Session::isTeacher() && !Session::isAdmin()) {
    require_once 'views/partials/header.php';
    echo '<div class="page-header"><h1>Acceso Denegado</h1></div>';
    echo '<div class="card" style="padding: 20px;"><p>No tienes permiso para acceder a esta sección.</p>';
    echo '<a href="index.php?view=dashboard" class="btn">Volver al Panel</a></div>';
    require_once 'views/partials/footer.php';
    exit();
}
// ---------------------------------

$asistenciaModel = new AsistenciaClienteModel();
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0); // id_matricula_detalle

try {
    switch ($action) {
        case 'marcar':
            if ($id <= 0) {
                throw new Exception("ID de matrícula no válido.");
            }

            $detalle_matricula = $asistenciaModel->obtenerDetalleMatricula($id);
            if (!$detalle_matricula) {
                throw new Exception("Detalle de matrícula no encontrado.");
            }

            // --- Lógica de Paginación ---
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $records_per_page = 10;
            $offset = ($page - 1) * $records_per_page;
            $total_records = $asistenciaModel->contarClases($id);
            $total_pages = ceil($total_records / $records_per_page);
            // -------------------------

            $clases = $asistenciaModel->obtenerClases($id, $records_per_page, $offset);

            // Traducir los nombres de los días
            foreach ($clases as &$clase) {
                $clase['dia_semana_es'] = get_day_name_es(date('N', strtotime($clase['fecha_clase'])));
            }

            require_once 'views/asistencia_clientes/form.php';
            break;

        case 'guardar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id_matricula_detalle = (int)($_POST['id_matricula_detalle'] ?? 0);
                $asistencias = $_POST['asistencias'] ?? [];

                if ($id_matricula_detalle <= 0) {
                    throw new Exception("ID de matrícula no válido.");
                }

                $updated_count = 0;
                foreach ($asistencias as $id_asistencia => $data) {
                    if ($asistenciaModel->actualizarAsistencia($id_asistencia, $data['estado'], $data['observaciones'])) {
                        $updated_count++;
                    }
                }

                $_SESSION['feedback_message'] = "Se actualizaron {$updated_count} registros de asistencia.";
                header('Location: index.php?view=asistencia_clientes');
                exit();
            }
            // Si no es POST, redirigir
            header('Location: index.php?view=asistencia_clientes');
            exit();

        case 'list':
        default:
            // --- Lógica de Filtros ---
            $filtros = [
                'id_cliente'    => !empty($_GET['filtro_cliente']) ? (int)$_GET['filtro_cliente'] : null,
                'id_curso'      => !empty($_GET['filtro_curso']) ? (int)$_GET['filtro_curso'] : null,
                'fecha_inicio'  => !empty($_GET['filtro_fecha_inicio']) ? $_GET['filtro_fecha_inicio'] : null,
                'fecha_fin'     => !empty($_GET['filtro_fecha_fin']) ? $_GET['filtro_fecha_fin'] : null
            ];

            // Datos para los dropdowns de los filtros
            $clienteModel = new ClienteModel();
            $cursosModel = new CursosModel();
            $lista_clientes = $clienteModel->obtenerTodos();
            $lista_cursos = $cursosModel->obtenerTodos();

            $matriculas = $asistenciaModel->listarMatriculas($filtros);
            require_once 'views/asistencia_clientes/list.php';
            break;
    }

} catch (Exception $e) {
    $_SESSION['feedback_message'] = "Error: " . $e->getMessage();
    header('Location: index.php?view=asistencia_clientes');
    exit();
}
