<?php

require_once 'models/AreasModel.php';

Session::check();

$model = new AreasModel();

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$feedback_message = '';

try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $model->crear($_POST['id_tipo_area'], $_POST['nombre']);
                $feedback_message = "Área creada exitosamente.";
            }
            break;
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                $model->actualizar($id, $_POST['id_tipo_area'], $_POST['nombre']);
                $feedback_message = "Área actualizada exitosamente.";
            }
            break;
        case 'delete':
            if ($id > 0) {
                if ($model->eliminar($id)) {
                    $feedback_message = "Área eliminada exitosamente.";
                } else {
                    $feedback_message = "Error: No se puede eliminar el área, podría estar en uso por una Sub Área.";
                }
            }
            break;
    }
} catch (Exception $e) {
    $feedback_message = "Error: " . $e->getMessage();
}

// Obtener datos para la vista
$items = $model->obtenerTodos();
$tipos_area = $model->obtenerTiposDeArea();

$item_a_editar = null;
if ($action === 'edit' && $id > 0) {
    $item_a_editar = $model->obtenerPorId($id);
}

require_once 'views/areas_view.php';
