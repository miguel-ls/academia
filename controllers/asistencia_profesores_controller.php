<?php

// =================================================================
// Controlador para la Asistencia de Profesores
// =================================================================

require_once 'models/AsistenciaProfesorModel.php';
require_once 'models/ProfesorModel.php';
require_once 'models/CursosModel.php';

// --- Verificación de Seguridad ---
Session::check();
if (!Session::isAdmin()) {
    require_once 'views/partials/header.php';
    echo '<div class="page-header"><h1>Acceso Denegado</h1></div>';
    echo '<div class="card" style="padding: 20px;"><p>No tienes permiso para acceder a esta sección.</p>';
    echo '<a href="index.php?view=dashboard" class="btn">Volver al Panel</a></div>';
    require_once 'views/partials/footer.php';
    exit();
}
// ---------------------------------

$asistenciaModel = new AsistenciaProfesorModel();
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id_curso_programado = (int)($_GET['id'] ?? 0);

try {
    switch ($action) {
        case 'marcar':
            if ($id_curso_programado > 0) {
                $detalle_curso = $asistenciaModel->obtenerDetalleCurso($id_curso_programado);
                $clases = $asistenciaModel->obtenerClases($id_curso_programado, PHP_INT_MAX, 0);

                if (!$detalle_curso) {
                    $_SESSION['feedback_message'] = "Error: El curso programado no fue encontrado.";
                    header('Location: index.php?view=asistencia_profesores');
                    exit();
                }

                require_once 'views/asistencia_profesor/form.php';
            } else {
                $_SESSION['feedback_message'] = "Error: ID de curso no válido.";
                header('Location: index.php?view=asistencia_profesores');
                exit();
            }
            break;

        case 'guardar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asistencia'])) {
                $asistencias = $_POST['asistencia'];
                $actualizaciones_exitosas = 0;
                $errores = 0;

                foreach ($asistencias as $id_asistencia => $datos) {
                    if ($asistenciaModel->actualizarAsistencia($id_asistencia, $datos['estado'], $datos['observaciones'])) {
                        $actualizaciones_exitosas++;
                    } else {
                        $errores++;
                    }
                }

                if ($errores > 0) {
                    $_SESSION['feedback_message'] = "Se actualizaron {$actualizaciones_exitosas} registros. Hubo {$errores} errores.";
                } else {
                    $_SESSION['feedback_message'] = "Se actualizaron {$actualizaciones_exitosas} registros de asistencia exitosamente.";
                }

            } else {
                 $_SESSION['feedback_message'] = "No se recibieron datos para guardar.";
            }
            $id_curso_guardado = (int)($_POST['id_curso_programado'] ?? 0);
            header('Location: index.php?view=asistencia_profesores&action=marcar&id=' . $id_curso_guardado);
            exit();

        case 'agregar_dias':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fecha_fin = $_POST['fecha_fin_nuevas'] ?? '';
                if ($id_curso_programado <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
                    throw new Exception('Seleccione una fecha final válida.');
                }
                $dias_agregados = $asistenciaModel->agregarDiasHasta($id_curso_programado, $fecha_fin);
                $_SESSION['feedback_message'] = "Se agregaron {$dias_agregados} días de clase nuevos.";
                header('Location: index.php?view=asistencia_profesores&action=marcar&id=' . $id_curso_programado);
                exit();
            }
            break;

        case 'eliminar_dia':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id_asistencia = (int)($_POST['id_asistencia_profesor'] ?? 0);
                if ($id_curso_programado <= 0 || $id_asistencia <= 0) {
                    throw new Exception('Registro de asistencia no válido.');
                }
                if (!$asistenciaModel->eliminarDia($id_asistencia)) {
                    throw new Exception('No se puede eliminar un día con estado Asistió.');
                }
                $_SESSION['feedback_message'] = 'Día de clase eliminado correctamente.';
                header('Location: index.php?view=asistencia_profesores&action=marcar&id=' . $id_curso_programado);
                exit();
            }
            break;

        case 'eliminar_dias_masivo':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $ids_asistencia = $_POST['ids_asistencia_profesor'] ?? [];
                if ($id_curso_programado <= 0 || empty($ids_asistencia)) {
                    throw new Exception('Seleccione al menos un día para eliminar.');
                }
                $eliminados = 0;
                foreach ($ids_asistencia as $id_asistencia) {
                    if ($asistenciaModel->eliminarDia((int)$id_asistencia)) {
                        $eliminados++;
                    }
                }
                $_SESSION['feedback_message'] = "Se eliminaron {$eliminados} días de clase.";
                header('Location: index.php?view=asistencia_profesores&action=marcar&id=' . $id_curso_programado);
                exit();
            }
            break;

        case 'cambiar_estado_masivo':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $ids_asistencia = $_POST['ids_asistencia_profesor'] ?? [];
                $nuevo_estado = $_POST['nuevo_estado'] ?? '';
                $estados_validos = ['Programado', 'Asistió', 'Faltó', 'Reprogramado'];

                if ($id_curso_programado <= 0 || empty($ids_asistencia)) {
                    throw new Exception('Seleccione al menos un día para cambiar de estado.');
                }
                if (!in_array($nuevo_estado, $estados_validos, true)) {
                    throw new Exception('Estado no válido.');
                }

                $actualizados = 0;
                foreach ($ids_asistencia as $id_asistencia) {
                    if ($asistenciaModel->actualizarEstado((int)$id_asistencia, $nuevo_estado)) {
                        $actualizados++;
                    }
                }
                $_SESSION['feedback_message'] = "Se actualizó el estado de {$actualizados} días.";
                header('Location: index.php?view=asistencia_profesores&action=marcar&id=' . $id_curso_programado);
                exit();
            }
            break;

        case 'list':
        default:
            // --- Lógica de Filtros ---
            $filtros = [
                'id_profesor'   => !empty($_GET['filtro_profesor']) ? (int)$_GET['filtro_profesor'] : null,
                'id_curso'      => !empty($_GET['filtro_curso']) ? (int)$_GET['filtro_curso'] : null,
                'fecha_inicio'  => !empty($_GET['filtro_fecha_inicio']) ? $_GET['filtro_fecha_inicio'] : null,
                'fecha_fin'     => !empty($_GET['filtro_fecha_fin']) ? $_GET['filtro_fecha_fin'] : null
            ];

            // Datos para los dropdowns de los filtros
            $profesorModel = new ProfesorModel();
            $cursosModel = new CursosModel();
            $lista_profesores = $profesorModel->obtenerTodos();
            $lista_cursos = $cursosModel->obtenerTodos();

            $cursos_programados = $asistenciaModel->listarCursosProgramados($filtros);
            require_once 'views/asistencia_profesor/list.php';
            break;
    }

} catch (Exception $e) {
    $_SESSION['feedback_message'] = "Error inesperado en el sistema: " . $e->getMessage();
    header('Location: index.php?view=asistencia_profesores');
    exit();
}
