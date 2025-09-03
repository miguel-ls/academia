<?php

// =================================================================
// Controlador para la Programación de Horarios (Refactorizado)
// =================================================================

require_once 'models/ProgramacionModel.php';

// --- Verificación de Seguridad ---
Session::check();
if (!Session::isAdmin()) {
    // Manejo de acceso denegado con la plantilla estándar
    require_once 'views/partials/header.php';
    echo '<div class="page-header"><h1>Acceso Denegado</h1></div>';
    echo '<div class="card" style="padding: 20px;"><p>No tienes permiso para acceder a esta sección.</p>';
    echo '<a href="index.php?view=dashboard" class="btn">Volver al Panel</a></div>';
    require_once 'views/partials/footer.php';
    exit();
}
// ---------------------------------

$programacionModel = new ProgramacionModel();
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

try {
    switch ($action) {
        case 'new':
            // Cargar datos para los dropdowns del formulario
            $listas_formulario = $programacionModel->obtenerListasParaFormulario();
            $cursos = $listas_formulario['cursos'];
            $profesores = $listas_formulario['profesores'];
            $sub_areas_raw = $listas_formulario['sub_areas'];
            $tipos_horario = $listas_formulario['tipos_horario'];

            // Formatear el nombre de la ubicación
            $sub_areas = [];
            foreach ($sub_areas_raw as $sub_area) {
                $sub_area['nombre_completo'] = $sub_area['area_nombre'] . ' - ' . $sub_area['descripcion'] . ' ' . $sub_area['numero_sub_area'];
                $sub_areas[] = $sub_area;
            }

            require_once 'views/programar_horarios/form.php';
            break;

        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // --- Validación de Cruce de Horarios ---
                $id_profesor_a_validar = (int)$_POST['id_profesor'];
                $dias_semana_nuevo = $programacionModel->getDiasSemanaByTipoHorarioId((int)$_POST['id_tipo_horario']);

                if ($dias_semana_nuevo) {
                    $dias_nuevos_arr = explode(',', $dias_semana_nuevo);
                    $horarios_existentes = $programacionModel->getProfesorSchedulesInRange($id_profesor_a_validar, $_POST['fecha_inicio'], $_POST['fecha_fin']);

                    foreach ($horarios_existentes as $horario) {
                        if ($_POST['hora_inicio'] < $horario['hora_fin'] && $horario['hora_inicio'] < $_POST['hora_fin']) {
                            $dias_existentes_arr = explode(',', $horario['dias_semana']);
                            if (count(array_intersect($dias_nuevos_arr, $dias_existentes_arr)) > 0) {
                                $_SESSION['feedback_message'] = "Error de validación: El profesor ya tiene un curso programado que se cruza con este horario (fecha, hora y día de la semana).";
                                header('Location: index.php?view=programar_horarios&action=new');
                                exit();
                            }
                        }
                    }
                }
                // --- Fin de Validación ---

                $datos = [
                    'id_curso' => $_POST['id_curso'],
                    'id_profesor' => $_POST['id_profesor'],
                    'id_sub_area' => $_POST['id_sub_area'],
                    'id_tipo_horario' => $_POST['id_tipo_horario'],
                    'fecha_inicio' => $_POST['fecha_inicio'],
                    'fecha_fin' => $_POST['fecha_fin'],
                    'hora_inicio' => $_POST['hora_inicio'],
                    'hora_fin' => $_POST['hora_fin'],
                    'vacantes' => $_POST['vacantes']
                ];
                if ($programacionModel->programarCurso($datos)) {
                    $_SESSION['feedback_message'] = "Curso programado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error: No se pudo programar el curso.";
                }
            }
            header('Location: index.php?view=programar_horarios');
            exit();

        case 'edit':
            if ($id > 0) {
                $programacion_a_editar = $programacionModel->obtenerPorId($id);
                if (!$programacion_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Programación no encontrada.";
                    header('Location: index.php?view=programar_horarios');
                    exit();
                }
                // Cargar datos para los dropdowns
                $listas_formulario = $programacionModel->obtenerListasParaFormulario();
                $cursos = $listas_formulario['cursos'];
                $profesores = $listas_formulario['profesores'];
                $sub_areas_raw = $listas_formulario['sub_areas'];
                $tipos_horario = $listas_formulario['tipos_horario'];
                $sub_areas = [];
                foreach ($sub_areas_raw as $sub_area) {
                    $sub_area['nombre_completo'] = $sub_area['area_nombre'] . ' - ' . $sub_area['descripcion'] . ' ' . $sub_area['numero_sub_area'];
                    $sub_areas[] = $sub_area;
                }

                require_once 'views/programar_horarios/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de programación no válido.";
                 header('Location: index.php?view=programar_horarios');
                 exit();
            }
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id_programacion = (int)($_POST['id_curso_programado'] ?? 0);
                 if ($id_programacion <= 0) {
                     $_SESSION['feedback_message'] = "Error: ID de programación no válido.";
                     header('Location: index.php?view=programar_horarios');
                     exit();
                }

                // --- Validación de Cruce de Horarios ---
                $id_profesor_a_validar = (int)$_POST['id_profesor'];
                $dias_semana_nuevo = $programacionModel->getDiasSemanaByTipoHorarioId((int)$_POST['id_tipo_horario']);

                if ($dias_semana_nuevo) {
                    $dias_nuevos_arr = explode(',', $dias_semana_nuevo);
                    $horarios_existentes = $programacionModel->getProfesorSchedulesInRange($id_profesor_a_validar, $_POST['fecha_inicio'], $_POST['fecha_fin'], $id_programacion);

                    foreach ($horarios_existentes as $horario) {
                        if ($_POST['hora_inicio'] < $horario['hora_fin'] && $horario['hora_inicio'] < $_POST['hora_fin']) {
                            $dias_existentes_arr = explode(',', $horario['dias_semana']);
                            if (count(array_intersect($dias_nuevos_arr, $dias_existentes_arr)) > 0) {
                                $_SESSION['feedback_message'] = "Error de validación: El profesor ya tiene un curso programado que se cruza con este horario (fecha, hora y día de la semana).";
                                header('Location: index.php?view=programar_horarios&action=edit&id=' . $id_programacion);
                                exit();
                            }
                        }
                    }
                }
                // --- Fin de Validación ---

                $datos = [
                    'id_curso_programado' => $id_programacion,
                    'id_curso' => $_POST['id_curso'],
                    'id_profesor' => $_POST['id_profesor'],
                    'id_sub_area' => $_POST['id_sub_area'],
                    'id_tipo_horario' => $_POST['id_tipo_horario'],
                    'fecha_inicio' => $_POST['fecha_inicio'],
                    'fecha_fin' => $_POST['fecha_fin'],
                    'hora_inicio' => $_POST['hora_inicio'],
                    'hora_fin' => $_POST['hora_fin'],
                    'vacantes' => $_POST['vacantes']
                ];
                if ($programacionModel->actualizar($datos)) {
                    $_SESSION['feedback_message'] = "Programación actualizada exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al actualizar la programación o no se realizaron cambios.";
                }
            }
            header('Location: index.php?view=programar_horarios');
            exit();

        case 'delete':
            if ($id > 0) {
                if ($programacionModel->eliminar($id)) {
                    $_SESSION['feedback_message'] = "Programación eliminada exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error: No se pudo eliminar la programación.";
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de programación no válido.";
            }
            header('Location: index.php?view=programar_horarios');
            exit();

        case 'list':
        default:
            $programaciones = $programacionModel->obtenerTodos();
            require_once 'views/programar_horarios/list.php';
            break;
    }

} catch (Exception $e) {
    $_SESSION['feedback_message'] = "Error inesperado en el sistema: " . $e->getMessage();
    header('Location: index.php?view=programar_horarios');
    exit();
}
