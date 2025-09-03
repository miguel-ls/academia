<?php
// =================================================================
// Controlador para el Mantenimiento de Areas (Refactorizado)
// =================================================================

require_once 'models/AreasModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$areasModel = new AreasModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$areas = [];
$item_a_editar = null;
$tipos_area = [];
$search_term = '';


try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_area' => $_POST['id_tipo_area'],
                    'nombre' => $_POST['nombre']
                ];
                $resultado = $areasModel->crear($datos);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Área creada exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el área: " . $resultado['error'];
                }
                header('Location: index.php?view=areas');
                exit();
            }
            header('Location: index.php?view=areas&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_area' => $_POST['id_area'],
                    'id_tipo_area' => $_POST['id_tipo_area'],
                    'nombre' => $_POST['nombre']
                ];
                $resultado = $areasModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Área actualizada exitosamente.";
                    header('Location: index.php?view=areas');
                    exit();
                } else {
                    $error_message = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                    $item_a_editar = $datos;
                    $tipos_area = $areasModel->obtenerTiposDeArea();
                    require_once 'views/areas/form.php';
                }
            } else {
                header('Location: index.php?view=areas');
                exit();
            }
            break;

        case 'delete':
            if ($id > 0) {
                $dependencias = $areasModel->verificarDependencias($id);
                if ($dependencias > 0) {
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar el área porque tiene {$dependencias} sub-área(s) asociada(s).";
                } else {
                    $resultado = $areasModel->eliminar($id);
                    if ($resultado['success']) {
                        $_SESSION['feedback_message'] = "Área eliminada exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo eliminar el área. " . ($resultado['error'] ?? '');
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de área no válido.";
            }
            header('Location: index.php?view=areas');
            exit();

        case 'new':
            $tipos_area = $areasModel->obtenerTiposDeArea();
            require_once 'views/areas/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $item_a_editar = $areasModel->obtenerPorId($id);
                $tipos_area = $areasModel->obtenerTiposDeArea();
                if (!$item_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Área no encontrada.";
                    header('Location: index.php?view=areas');
                    exit();
                }
                require_once 'views/areas/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de área no válido.";
                 header('Location: index.php?view=areas');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $areas = $areasModel->buscar($search_term);
            } else {
                $areas = $areasModel->obtenerTodos();
            }
            require_once 'views/areas/list.php';
            break;
    }

} catch (Exception $e) {
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    $areas = $areasModel->obtenerTodos();
    require_once 'views/areas/list.php';
}
