<?php
// =================================================================
// Controlador para el Mantenimiento de Tipos de Curso (Refactorizado)
// =================================================================

require_once 'models/TiposCursoModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$tiposCursoModel = new TiposCursoModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$tipos_curso = [];
$item_a_editar = null;
$search_term = '';


try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion']
                ];
                $resultado = $tiposCursoModel->crear($datos);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Tipo de curso creado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el tipo de curso: " . $resultado['error'];
                }
                header('Location: index.php?view=tipos_curso');
                exit();
            }
            header('Location: index.php?view=tipos_curso&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_curso' => $_POST['id_tipo_curso'],
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion']
                ];
                $resultado = $tiposCursoModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Tipo de curso actualizado exitosamente.";
                    header('Location: index.php?view=tipos_curso');
                    exit();
                } else {
                    $error_message = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                    $item_a_editar = $datos;
                    require_once 'views/tipos_curso/form.php';
                }
            } else {
                header('Location: index.php?view=tipos_curso');
                exit();
            }
            break;

        case 'delete':
            if ($id > 0) {
                $dependencias = $tiposCursoModel->verificarDependencias($id);
                if ($dependencias > 0) {
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar el tipo de curso porque tiene {$dependencias} curso(s) asociado(s).";
                } else {
                    $resultado = $tiposCursoModel->eliminar($id);
                    if ($resultado['success']) {
                        $_SESSION['feedback_message'] = "Tipo de curso eliminado exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo eliminar el tipo de curso. " . ($resultado['error'] ?? '');
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de tipo de curso no válido.";
            }
            header('Location: index.php?view=tipos_curso');
            exit();

        case 'new':
            require_once 'views/tipos_curso/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $item_a_editar = $tiposCursoModel->obtenerPorId($id);
                if (!$item_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Tipo de curso no encontrado.";
                    header('Location: index.php?view=tipos_curso');
                    exit();
                }
                require_once 'views/tipos_curso/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de tipo de curso no válido.";
                 header('Location: index.php?view=tipos_curso');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $tipos_curso = $tiposCursoModel->buscar($search_term);
            } else {
                $tipos_curso = $tiposCursoModel->obtenerTodos();
            }
            require_once 'views/tipos_curso/list.php';
            break;
    }

} catch (Exception $e) {
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    $tipos_curso = $tiposCursoModel->obtenerTodos();
    require_once 'views/tipos_curso/list.php';
}
