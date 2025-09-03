<?php

// =================================================================
// Controlador para el Mantenimiento de Profesores
// =================================================================

require_once 'models/ProfesorModel.php';
require_once 'models/TiposDocumentoModel.php';
require_once 'models/TiposCursoModel.php';

// --- Verificación de Seguridad ---
Session::check();
if (!Session::isAdmin()) {
    require_once 'views/partials/header.php';
    echo '<div class="page-header"><h1>Acceso Denegado</h1></div>';
    echo '<div class="card" style="padding: 20px;"><p>No tienes permiso para acceder a esta sección.</p>';
    echo '<a href="index.php?view=dashboard" class="btn">Volver al Panel</a></div>';
    require_once 'views/partials/footer.php';
    exit();
}
// ---------------------------------

$profesorModel = new ProfesorModel();
$tiposDocumentoModel = new TiposDocumentoModel();
$tiposCursoModel = new TiposCursoModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$profesores = [];
$profesor_a_editar = null;
$tipos_documento = [];
$tipos_curso = [];
$search_term = '';

try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_documento' => $_POST['id_tipo_documento'],
                    'numero_documento' => $_POST['numero_documento'],
                    'nombres' => $_POST['nombres'],
                    'apellidos' => $_POST['apellidos'],
                    'email' => $_POST['email'],
                    'telefono' => $_POST['telefono'],
                    'especialidad' => $_POST['especialidad']
                ];
                if ($profesorModel->crear($datos)) {
                    $_SESSION['feedback_message'] = "Profesor creado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el profesor.";
                }
                header('Location: index.php?view=profesores');
                exit();
            }
            header('Location: index.php?view=profesores&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id_profesor = (int)($_POST['id_profesor'] ?? 0);
                if ($id_profesor <= 0) {
                     $_SESSION['feedback_message'] = "Error: ID de profesor no válido.";
                     header('Location: index.php?view=profesores');
                     exit();
                }
                $datos = [
                    'id_profesor' => $id_profesor,
                    'id_tipo_documento' => $_POST['id_tipo_documento'],
                    'numero_documento' => $_POST['numero_documento'],
                    'nombres' => $_POST['nombres'],
                    'apellidos' => $_POST['apellidos'],
                    'email' => $_POST['email'],
                    'telefono' => $_POST['telefono'],
                    'especialidad' => $_POST['especialidad']
                ];
                if ($profesorModel->actualizar($datos)) {
                    $_SESSION['feedback_message'] = "Profesor actualizado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al actualizar el profesor o no se realizaron cambios.";
                }
                header('Location: index.php?view=profesores');
                exit();
            }
            header('Location: index.php?view=profesores');
            exit();

        case 'delete':
            if ($id > 0) {
                // Aquí se podría añadir una verificación de dependencias
                if ($profesorModel->eliminar($id)) {
                    $_SESSION['feedback_message'] = "Profesor eliminado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error: No se pudo eliminar el profesor.";
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de profesor no válido.";
            }
            header('Location: index.php?view=profesores');
            exit();

        case 'buscar':
            header('Content-Type: application/json');
            $termino = $_GET['q'] ?? '';
            $resultados = $profesorModel->buscar($termino);
            echo json_encode($resultados);
            exit();

        case 'new':
            $tipos_documento = $tiposDocumentoModel->obtenerTodos();
            $tipos_curso = $tiposCursoModel->obtenerTodos();
            require_once 'views/profesores/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $profesor_a_editar = $profesorModel->obtenerPorId($id);
                if (!$profesor_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Profesor no encontrado.";
                    header('Location: index.php?view=profesores');
                    exit();
                }
                $tipos_documento = $tiposDocumentoModel->obtenerTodos();
                $tipos_curso = $tiposCursoModel->obtenerTodos();
                require_once 'views/profesores/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de profesor no válido.";
                 header('Location: index.php?view=profesores');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            // La búsqueda en la lista principal no está implementada en el SP,
            // así que por ahora simplemente listamos todos.
            $profesores = $profesorModel->obtenerTodos();
            require_once 'views/profesores/list.php';
            break;
    }

} catch (Exception $e) {
    $_SESSION['feedback_message'] = "Error inesperado en el sistema: " . $e->getMessage();
    header('Location: index.php?view=profesores');
    exit();
}
