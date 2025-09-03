<?php
// =================================================================
// Controlador para el Mantenimiento de Cursos (Refactorizado)
// =================================================================

require_once 'models/CursosModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$cursosModel = new CursosModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$cursos = [];
$curso_a_editar = null;
$tipos_curso = [];
$search_term = '';


try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_curso' => $_POST['id_tipo_curso'],
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'],
                    'codigo_erp' => $_POST['codigo_erp']
                ];
                $resultado = $cursosModel->crear($datos);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Curso creado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el curso: " . $resultado['error'];
                }
                header('Location: index.php?view=cursos');
                exit();
            }
            header('Location: index.php?view=cursos&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_curso' => $_POST['id_curso'],
                    'id_tipo_curso' => $_POST['id_tipo_curso'],
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'],
                    'codigo_erp' => $_POST['codigo_erp']
                ];
                $resultado = $cursosModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Curso actualizado exitosamente.";
                    header('Location: index.php?view=cursos');
                    exit();
                } else {
                    $error_message = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                    $curso_a_editar = $datos;
                    $tipos_curso = $cursosModel->obtenerTiposDeCurso();
                    require_once 'views/cursos/form.php';
                }
            } else {
                header('Location: index.php?view=cursos');
                exit();
            }
            break;

        case 'delete':
            if ($id > 0) {
                $dependencias = $cursosModel->verificarDependencias($id);
                if ($dependencias > 0) {
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar el curso porque tiene {$dependencias} matrícula(s) asociada(s).";
                } else {
                    $resultado = $cursosModel->eliminar($id);
                    if ($resultado['success']) {
                        $_SESSION['feedback_message'] = "Curso eliminado exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo eliminar el curso. " . ($resultado['error'] ?? '');
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de curso no válido.";
            }
            header('Location: index.php?view=cursos');
            exit();

        case 'new':
            $tipos_curso = $cursosModel->obtenerTiposDeCurso();
            require_once 'views/cursos/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $curso_a_editar = $cursosModel->obtenerPorId($id);
                $tipos_curso = $cursosModel->obtenerTiposDeCurso();
                if (!$curso_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Curso no encontrado.";
                    header('Location: index.php?view=cursos');
                    exit();
                }
                require_once 'views/cursos/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de curso no válido.";
                 header('Location: index.php?view=cursos');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $cursos = $cursosModel->buscar($search_term);
            } else {
                $cursos = $cursosModel->obtenerTodos();
            }
            require_once 'views/cursos/list.php';
            break;
    }

} catch (Exception $e) {
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    require_once 'views/cursos/list.php';
}
