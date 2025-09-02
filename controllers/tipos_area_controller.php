<?php
// =================================================================
// Controlador para el Mantenimiento de Tipos de Área (Refactorizado)
// =================================================================

require_once 'models/TiposAreaModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$tiposAreaModel = new TiposAreaModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$tipos_area = [];
$item_a_editar = null;
$search_term = '';


try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = ['nombre' => $_POST['nombre']];
                $resultado = $tiposAreaModel->crear($datos);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Tipo de área creado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el tipo de área: " . $resultado['error'];
                }
                header('Location: index.php?view=tipos_area');
                exit();
            }
            header('Location: index.php?view=tipos_area&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_area' => $_POST['id_tipo_area'],
                    'nombre' => $_POST['nombre']
                ];
                $resultado = $tiposAreaModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Tipo de área actualizado exitosamente.";
                    header('Location: index.php?view=tipos_area');
                    exit();
                } else {
                    $error_message = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                    $item_a_editar = $datos;
                    require_once 'views/tipos_area/form.php';
                }
            } else {
                header('Location: index.php?view=tipos_area');
                exit();
            }
            break;

        case 'delete':
            if ($id > 0) {
                $dependencias = $tiposAreaModel->verificarDependencias($id);
                if ($dependencias > 0) {
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar el tipo de área porque tiene {$dependencias} área(s) asociada(s).";
                } else {
                    $resultado = $tiposAreaModel->eliminar($id);
                    if ($resultado['success']) {
                        $_SESSION['feedback_message'] = "Tipo de área eliminado exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo eliminar el tipo de área. " . ($resultado['error'] ?? '');
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de tipo de área no válido.";
            }
            header('Location: index.php?view=tipos_area');
            exit();

        case 'new':
            require_once 'views/tipos_area/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $item_a_editar = $tiposAreaModel->obtenerPorId($id);
                if (!$item_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Tipo de área no encontrado.";
                    header('Location: index.php?view=tipos_area');
                    exit();
                }
                require_once 'views/tipos_area/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de tipo de área no válido.";
                 header('Location: index.php?view=tipos_area');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $tipos_area = $tiposAreaModel->buscar($search_term);
            } else {
                $tipos_area = $tiposAreaModel->obtenerTodos();
            }
            require_once 'views/tipos_area/list.php';
            break;
    }

} catch (Exception $e) {
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    $tipos_area = $tiposAreaModel->obtenerTodos();
    require_once 'views/tipos_area/list.php';
}
