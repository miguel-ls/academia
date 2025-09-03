<?php
// =================================================================
// Controlador para el Mantenimiento de Tipos de Horario (Refactorizado)
// =================================================================

require_once 'models/TiposHorarioModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$tiposHorarioModel = new TiposHorarioModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$tipos_horario = [];
$item_a_editar = null;
$search_term = '';


try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'descripcion' => $_POST['descripcion'],
                    'dias_semana' => isset($_POST['dias_semana']) ? implode(',', $_POST['dias_semana']) : ''
                ];
                $resultado = $tiposHorarioModel->crear($datos);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Tipo de horario creado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el tipo de horario: " . $resultado['error'];
                }
                header('Location: index.php?view=tipos_horario');
                exit();
            }
            header('Location: index.php?view=tipos_horario&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_horario' => $_POST['id_tipo_horario'],
                    'descripcion' => $_POST['descripcion'],
                    'dias_semana' => isset($_POST['dias_semana']) ? implode(',', $_POST['dias_semana']) : ''
                ];
                $resultado = $tiposHorarioModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Tipo de horario actualizado exitosamente.";
                    header('Location: index.php?view=tipos_horario');
                    exit();
                } else {
                    $error_message = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                    $item_a_editar = $datos;
                    require_once 'views/tipos_horario/form.php';
                }
            } else {
                header('Location: index.php?view=tipos_horario');
                exit();
            }
            break;

        case 'delete':
            if ($id > 0) {
                $dependencias = $tiposHorarioModel->verificarDependencias($id);
                if ($dependencias > 0) {
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar el tipo de horario porque tiene {$dependencias} programacion(es) asociada(s).";
                } else {
                    $resultado = $tiposHorarioModel->eliminar($id);
                    if ($resultado['success']) {
                        $_SESSION['feedback_message'] = "Tipo de horario eliminado exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo eliminar el tipo de horario. " . ($resultado['error'] ?? '');
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de tipo de horario no válido.";
            }
            header('Location: index.php?view=tipos_horario');
            exit();

        case 'new':
            require_once 'views/tipos_horario/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $item_a_editar = $tiposHorarioModel->obtenerPorId($id);
                if (!$item_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Tipo de horario no encontrado.";
                    header('Location: index.php?view=tipos_horario');
                    exit();
                }
                require_once 'views/tipos_horario/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de tipo de horario no válido.";
                 header('Location: index.php?view=tipos_horario');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $tipos_horario = $tiposHorarioModel->buscar($search_term);
            } else {
                $tipos_horario = $tiposHorarioModel->obtenerTodos();
            }
            require_once 'views/tipos_horario/list.php';
            break;
    }

} catch (Exception $e) {
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    $tipos_horario = $tiposHorarioModel->obtenerTodos();
    require_once 'views/tipos_horario/list.php';
}
