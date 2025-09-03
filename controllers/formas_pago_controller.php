<?php
// =================================================================
// Controlador para el Mantenimiento de Formas de Pago (Refactorizado)
// =================================================================

require_once 'models/FormasPagoModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$formasPagoModel = new FormasPagoModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$formas_pago = [];
$item_a_editar = null;
$search_term = '';


try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = ['nombre' => $_POST['nombre']];
                $resultado = $formasPagoModel->crear($datos);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Forma de pago creada exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear la forma de pago: " . $resultado['error'];
                }
                header('Location: index.php?view=formas_pago');
                exit();
            }
            header('Location: index.php?view=formas_pago&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_forma_pago' => $_POST['id_forma_pago'],
                    'nombre' => $_POST['nombre']
                ];
                $resultado = $formasPagoModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Forma de pago actualizada exitosamente.";
                    header('Location: index.php?view=formas_pago');
                    exit();
                } else {
                    $error_message = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                    $item_a_editar = $datos;
                    require_once 'views/formas_pago/form.php';
                }
            } else {
                header('Location: index.php?view=formas_pago');
                exit();
            }
            break;

        case 'delete':
            if ($id > 0) {
                $dependencias = $formasPagoModel->verificarDependencias($id);
                if ($dependencias > 0) {
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar la forma de pago porque tiene {$dependencias} matrícula(s) asociada(s).";
                } else {
                    $resultado = $formasPagoModel->eliminar($id);
                    if ($resultado['success']) {
                        $_SESSION['feedback_message'] = "Forma de pago eliminada exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo eliminar la forma de pago. " . ($resultado['error'] ?? '');
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de forma de pago no válido.";
            }
            header('Location: index.php?view=formas_pago');
            exit();

        case 'new':
            require_once 'views/formas_pago/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $item_a_editar = $formasPagoModel->obtenerPorId($id);
                if (!$item_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Forma de pago no encontrada.";
                    header('Location: index.php?view=formas_pago');
                    exit();
                }
                require_once 'views/formas_pago/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de forma de pago no válido.";
                 header('Location: index.php?view=formas_pago');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $formas_pago = $formasPagoModel->buscar($search_term);
            } else {
                $formas_pago = $formasPagoModel->obtenerTodos();
            }
            require_once 'views/formas_pago/list.php';
            break;
    }

} catch (Exception $e) {
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    $formas_pago = $formasPagoModel->obtenerTodos();
    require_once 'views/formas_pago/list.php';
}
