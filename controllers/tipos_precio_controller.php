<?php
// =================================================================
// Controlador para el Mantenimiento de Tipos de Precio (Refactorizado)
// =================================================================

require_once 'models/TiposPrecioModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$tiposPrecioModel = new TiposPrecioModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$tipos_precio = [];
$item_a_editar = null;
$search_term = '';


try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = ['nombre' => $_POST['nombre']];
                $resultado = $tiposPrecioModel->crear($datos);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Tipo de precio creado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el tipo de precio: " . $resultado['error'];
                }
                header('Location: index.php?view=tipos_precio');
                exit();
            }
            header('Location: index.php?view=tipos_precio&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_precio' => $_POST['id_tipo_precio'],
                    'nombre' => $_POST['nombre']
                ];
                $resultado = $tiposPrecioModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Tipo de precio actualizado exitosamente.";
                    header('Location: index.php?view=tipos_precio');
                    exit();
                } else {
                    $error_message = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                    $item_a_editar = $datos;
                    require_once 'views/tipos_precio/form.php';
                }
            } else {
                header('Location: index.php?view=tipos_precio');
                exit();
            }
            break;

        case 'delete':
            if ($id > 0) {
                $dependencias = $tiposPrecioModel->verificarDependencias($id);
                if ($dependencias > 0) {
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar el tipo de precio porque tiene {$dependencias} lista(s) de precios asociada(s).";
                } else {
                    $resultado = $tiposPrecioModel->eliminar($id);
                    if ($resultado['success']) {
                        $_SESSION['feedback_message'] = "Tipo de precio eliminado exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo eliminar el tipo de precio. " . ($resultado['error'] ?? '');
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de tipo de precio no válido.";
            }
            header('Location: index.php?view=tipos_precio');
            exit();

        case 'new':
            require_once 'views/tipos_precio/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $item_a_editar = $tiposPrecioModel->obtenerPorId($id);
                if (!$item_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Tipo de precio no encontrado.";
                    header('Location: index.php?view=tipos_precio');
                    exit();
                }
                require_once 'views/tipos_precio/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de tipo de precio no válido.";
                 header('Location: index.php?view=tipos_precio');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $tipos_precio = $tiposPrecioModel->buscar($search_term);
            } else {
                $tipos_precio = $tiposPrecioModel->obtenerTodos();
            }
            require_once 'views/tipos_precio/list.php';
            break;
    }

} catch (Exception $e) {
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    $tipos_precio = $tiposPrecioModel->obtenerTodos();
    require_once 'views/tipos_precio/list.php';
}
