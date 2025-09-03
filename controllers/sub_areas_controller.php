<?php
// =================================================================
// Controlador para el Mantenimiento de Sub Areas (Refactorizado)
// =================================================================

require_once 'models/SubAreasModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$subAreasModel = new SubAreasModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$sub_areas = [];
$item_a_editar = null;
$areas = [];
$search_term = '';


try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_area' => $_POST['id_area'],
                    'descripcion' => $_POST['descripcion'],
                    'numero_sub_area' => $_POST['numero_sub_area'],
                    'capacidad_maxima' => $_POST['capacidad_maxima']
                ];
                $resultado = $subAreasModel->crear($datos);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Sub Área creada exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear la sub área: " . $resultado['error'];
                }
                header('Location: index.php?view=sub_areas');
                exit();
            }
            header('Location: index.php?view=sub_areas&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_sub_area' => $_POST['id_sub_area'],
                    'id_area' => $_POST['id_area'],
                    'descripcion' => $_POST['descripcion'],
                    'numero_sub_area' => $_POST['numero_sub_area'],
                    'capacidad_maxima' => $_POST['capacidad_maxima']
                ];
                $resultado = $subAreasModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Sub Área actualizada exitosamente.";
                    header('Location: index.php?view=sub_areas');
                    exit();
                } else {
                    $error_message = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                    $item_a_editar = $datos;
                    $areas = $subAreasModel->obtenerAreas();
                    require_once 'views/sub_areas/form.php';
                }
            } else {
                header('Location: index.php?view=sub_areas');
                exit();
            }
            break;

        case 'delete':
            if ($id > 0) {
                $dependencias = $subAreasModel->verificarDependencias($id);
                if ($dependencias > 0) {
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar la sub área porque tiene {$dependencias} horario(s) programado(s).";
                } else {
                    $resultado = $subAreasModel->eliminar($id);
                    if ($resultado['success']) {
                        $_SESSION['feedback_message'] = "Sub Área eliminada exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo eliminar la sub área. " . ($resultado['error'] ?? '');
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de sub área no válido.";
            }
            header('Location: index.php?view=sub_areas');
            exit();

        case 'new':
            $areas = $subAreasModel->obtenerAreas();
            require_once 'views/sub_areas/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $item_a_editar = $subAreasModel->obtenerPorId($id);
                $areas = $subAreasModel->obtenerAreas();
                if (!$item_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Sub Área no encontrada.";
                    header('Location: index.php?view=sub_areas');
                    exit();
                }
                require_once 'views/sub_areas/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de sub área no válido.";
                 header('Location: index.php?view=sub_areas');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $sub_areas = $subAreasModel->buscar($search_term);
            } else {
                $sub_areas = $subAreasModel->obtenerTodos();
            }
            require_once 'views/sub_areas/list.php';
            break;
    }

} catch (Exception $e) {
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    $sub_areas = $subAreasModel->obtenerTodos();
    require_once 'views/sub_areas/list.php';
}
