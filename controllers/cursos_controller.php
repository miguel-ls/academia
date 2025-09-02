<?php

require_once 'models/CursosModel.php';

Session::check();

$model = new CursosModel();

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$feedback_message = '';

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
                $model->crear($datos);
                $feedback_message = "Curso creado exitosamente.";
            }
            break;
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                 $datos = [
                    'id_curso' => $id,
                    'id_tipo_curso' => $_POST['id_tipo_curso'],
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'],
                    'codigo_erp' => $_POST['codigo_erp']
                ];
                $model->actualizar($datos);
                $feedback_message = "Curso actualizado exitosamente.";
            }
            break;
        case 'delete':
            if ($id > 0) {
                if ($model->eliminar($id)) {
                    $feedback_message = "Curso eliminado exitosamente.";
                } else {
                    $feedback_message = "Error: No se puede eliminar el curso, podría estar en uso.";
                }
            }
            break;
    }
} catch (Exception $e) {
    $feedback_message = "Error: " . $e->getMessage();
}

// Obtener datos para la vista
$items = $model->obtenerTodos();
$tipos_curso = $model->obtenerTiposDeCurso(); // Para el dropdown

$item_a_editar = null;
if ($action === 'edit' && $id > 0) {
    $item_a_editar = $model->obtenerPorId($id);
}

require_once 'views/cursos_view.php';
