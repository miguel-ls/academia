<?php
// =================================================================
// Controlador para el Mantenimiento de Tipos de Documento (Refactorizado)
// =================================================================

require_once 'models/TiposDocumentoModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$tiposDocumentoModel = new TiposDocumentoModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$tipos_documento = [];
$item_a_editar = null;
$search_term = '';


try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'descripcion' => $_POST['descripcion'],
                    'longitud' => !empty($_POST['longitud']) ? $_POST['longitud'] : null,
                    'codigo_sunat' => !empty($_POST['codigo_sunat']) ? $_POST['codigo_sunat'] : null
                ];
                $resultado = $tiposDocumentoModel->crear($datos);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Tipo de documento creado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el tipo de documento: " . $resultado['error'];
                }
                header('Location: index.php?view=tipos_documento');
                exit();
            }
            header('Location: index.php?view=tipos_documento&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_documento' => $_POST['id_tipo_documento'],
                    'descripcion' => $_POST['descripcion'],
                    'longitud' => !empty($_POST['longitud']) ? $_POST['longitud'] : null,
                    'codigo_sunat' => !empty($_POST['codigo_sunat']) ? $_POST['codigo_sunat'] : null
                ];
                $resultado = $tiposDocumentoModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Tipo de documento actualizado exitosamente.";
                    header('Location: index.php?view=tipos_documento');
                    exit();
                } else {
                    $error_message = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                    $item_a_editar = $datos;
                    require_once 'views/tipos_documento/form.php';
                }
            } else {
                header('Location: index.php?view=tipos_documento');
                exit();
            }
            break;

        case 'delete':
            if ($id > 0) {
                $dependencias = $tiposDocumentoModel->verificarDependencias($id);
                if ($dependencias > 0) {
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar el tipo de documento porque tiene {$dependencias} cliente(s) asociado(s).";
                } else {
                    $resultado = $tiposDocumentoModel->eliminar($id);
                    if ($resultado['success']) {
                        $_SESSION['feedback_message'] = "Tipo de documento eliminado exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo eliminar el tipo de documento. " . ($resultado['error'] ?? '');
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de tipo de documento no válido.";
            }
            header('Location: index.php?view=tipos_documento');
            exit();

        case 'new':
            require_once 'views/tipos_documento/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $item_a_editar = $tiposDocumentoModel->obtenerPorId($id);
                if (!$item_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Tipo de documento no encontrado.";
                    header('Location: index.php?view=tipos_documento');
                    exit();
                }
                require_once 'views/tipos_documento/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de tipo de documento no válido.";
                 header('Location: index.php?view=tipos_documento');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $tipos_documento = $tiposDocumentoModel->buscar($search_term);
            } else {
                $tipos_documento = $tiposDocumentoModel->obtenerTodos();
            }
            require_once 'views/tipos_documento/list.php';
            break;
    }

} catch (Exception $e) {
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    $tipos_documento = $tiposDocumentoModel->obtenerTodos();
    require_once 'views/tipos_documento/list.php';
}
