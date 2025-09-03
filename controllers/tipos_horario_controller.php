<?php

require_once 'models/TiposHorarioModel.php';

Session::check();

$model = new TiposHorarioModel();

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$feedback_message = '';

try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'descripcion' => $_POST['descripcion'],
                    'dias_semana' => implode(',', $_POST['dias_semana'] ?? [])
                ];
                $model->crear($datos);
                $feedback_message = "Tipo de Horario creado exitosamente.";
            }
            break;
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                 $datos = [
                    'id' => $id,
                    'descripcion' => $_POST['descripcion'],
                    'dias_semana' => implode(',', $_POST['dias_semana'] ?? [])
                ];
                $model->actualizar($datos);
                $feedback_message = "Tipo de Horario actualizado exitosamente.";
            }
            break;
        case 'delete':
            if ($id > 0) {
                if ($model->eliminar($id)) {
                    $feedback_message = "Tipo de Horario eliminado exitosamente.";
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

require_once 'views/tipos_horario_view.php';
