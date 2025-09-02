<?php

require_once 'models/SubAreasModel.php';

Session::check();

$model = new SubAreasModel();

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$feedback_message = '';

try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_area' => $_POST['id_area'],
                    'descripcion' => $_POST['descripcion'],
                    'numero_sub_area' => $_POST['numero_sub_area'],
                    'capacidad_maxima' => $_POST['capacidad_maxima']
                ];
                $model->crear($datos);
                $feedback_message = "Sub Área creada exitosamente.";
            }
            break;
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                 $datos = [
                    'id_sub_area' => $id,
                    'id_area' => $_POST['id_area'],
                    'descripcion' => $_POST['descripcion'],
                    'numero_sub_area' => $_POST['numero_sub_area'],
                    'capacidad_maxima' => $_POST['capacidad_maxima']
                ];
                $model->actualizar($datos);
                $feedback_message = "Sub Área actualizada exitosamente.";
            }
            break;
        case 'delete':
            if ($id > 0) {
                if ($model->eliminar($id)) {
                    $feedback_message = "Sub Área eliminada exitosamente.";
                } else {
                    $feedback_message = "Error: No se puede eliminar la sub área, podría estar en uso.";
                }
            }
            break;
    }
} catch (Exception $e) {
    $feedback_message = "Error: " . $e->getMessage();
}

// Obtener datos para la vista
$items = $model->obtenerTodos();
$areas = $model->obtenerAreas(); // Para el dropdown

$item_a_editar = null;
if ($action === 'edit' && $id > 0) {
    $item_a_editar = $model->obtenerPorId($id);
}

require_once 'views/sub_areas_view.php';
