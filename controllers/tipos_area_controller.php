<?php

require_once 'models/TiposAreaModel.php';

Session::check(); // Proteger la página

$model = new TiposAreaModel();

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$feedback_message = '';

try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $model->crear($_POST['nombre']);
                $feedback_message = "Tipo de Área creado exitosamente.";
            }
            break;
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                $model->actualizar($id, $_POST['nombre']);
                $feedback_message = "Tipo de Área actualizado exitosamente.";
            }
            break;
        case 'delete':
            if ($id > 0) {
                if ($model->eliminar($id)) {
                    $feedback_message = "Tipo de Área eliminado exitosamente.";
                } else {
                    $feedback_message = "Error: No se puede eliminar el tipo de área porque está en uso.";
                }
            }
            break;
    }
} catch (Exception $e) {
    $feedback_message = "Error: " . $e->getMessage();
}

$items = $model->obtenerTodos();

$item_a_editar = null;
if ($action === 'edit' && $id > 0) {
    $item_a_editar = $model->obtenerPorId($id);
}

require_once 'views/tipos_area_view.php';
