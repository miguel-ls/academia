<?php

require_once 'models/FormasPagoModel.php';

Session::check();

$model = new FormasPagoModel();

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$feedback_message = '';

try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $model->crear($_POST['nombre']);
                $feedback_message = "Forma de Pago creada exitosamente.";
            }
            break;
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                $model->actualizar($id, $_POST['nombre']);
                $feedback_message = "Forma de Pago actualizada exitosamente.";
            }
            break;
        case 'delete':
            if ($id > 0) {
                if ($model->eliminar($id)) {
                    $feedback_message = "Forma de Pago eliminada exitosamente.";
                } else {
                    $feedback_message = "Error: No se puede eliminar, podría estar en uso.";
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

require_once 'views/formas_pago_view.php';
