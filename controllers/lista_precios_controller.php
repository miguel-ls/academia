<?php
// =================================================================
// Controlador para el Mantenimiento de Lista de Precios (Refactorizado)
// =================================================================

require_once 'models/ListaPreciosModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$listaPreciosModel = new ListaPreciosModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$lista_precios = [];
$item_a_editar = null;
$cursos = [];
$tipos_precio = [];
$search_term = '';


try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_curso' => $_POST['id_curso'],
                    'id_tipo_precio' => $_POST['id_tipo_precio'],
                    'precio' => $_POST['precio'],
                    'vigencia_inicio' => $_POST['vigencia_inicio'],
                    'vigencia_fin' => $_POST['vigencia_fin']
                ];
                $resultado = $listaPreciosModel->crear($datos);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Precio creado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el precio: " . $resultado['error'];
                }
                header('Location: index.php?view=lista_precios');
                exit();
            }
            header('Location: index.php?view=lista_precios&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_lista_precio' => $_POST['id_lista_precio'],
                    'id_curso' => $_POST['id_curso'],
                    'id_tipo_precio' => $_POST['id_tipo_precio'],
                    'precio' => $_POST['precio'],
                    'vigencia_inicio' => $_POST['vigencia_inicio'],
                    'vigencia_fin' => $_POST['vigencia_fin']
                ];
                $resultado = $listaPreciosModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Precio actualizado exitosamente.";
                    header('Location: index.php?view=lista_precios');
                    exit();
                } else {
                    $error_message = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                    $item_a_editar = $datos;
                    $cursos = $listaPreciosModel->obtenerCursos();
                    $tipos_precio = $listaPreciosModel->obtenerTiposDePrecio();
                    require_once 'views/lista_precios/form.php';
                }
            } else {
                header('Location: index.php?view=lista_precios');
                exit();
            }
            break;

        case 'delete':
            if ($id > 0) {
                // No se verifica dependencias ya que no se han identificado FK hacia esta tabla.
                $resultado = $listaPreciosModel->eliminar($id);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Precio eliminado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error: No se pudo eliminar el precio. " . ($resultado['error'] ?? '');
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de precio no válido.";
            }
            header('Location: index.php?view=lista_precios');
            exit();

        case 'new':
            $cursos = $listaPreciosModel->obtenerCursos();
            $tipos_precio = $listaPreciosModel->obtenerTiposDePrecio();
            require_once 'views/lista_precios/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $item_a_editar = $listaPreciosModel->obtenerPorId($id);
                $cursos = $listaPreciosModel->obtenerCursos();
                $tipos_precio = $listaPreciosModel->obtenerTiposDePrecio();
                if (!$item_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Precio no encontrado.";
                    header('Location: index.php?view=lista_precios');
                    exit();
                }
                require_once 'views/lista_precios/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de precio no válido.";
                 header('Location: index.php?view=lista_precios');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $lista_precios = $listaPreciosModel->buscar($search_term);
            } else {
                $lista_precios = $listaPreciosModel->obtenerTodos();
            }
            require_once 'views/lista_precios/list.php';
            break;
    }

} catch (Exception $e) {
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    $lista_precios = $listaPreciosModel->obtenerTodos();
    require_once 'views/lista_precios/list.php';
}
