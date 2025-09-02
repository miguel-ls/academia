<?php

require_once 'models/ListaPreciosModel.php';

Session::check();

$model = new ListaPreciosModel();

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$feedback_message = '';

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
                $model->crear($datos);
                $feedback_message = "Precio creado exitosamente.";
            }
            break;
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                 $datos = [
                    'id' => $id,
                    'id_curso' => $_POST['id_curso'],
                    'id_tipo_precio' => $_POST['id_tipo_precio'],
                    'precio' => $_POST['precio'],
                    'vigencia_inicio' => $_POST['vigencia_inicio'],
                    'vigencia_fin' => $_POST['vigencia_fin']
                ];
                $model->actualizar($datos);
                $feedback_message = "Precio actualizado exitosamente.";
            }
            break;
        case 'delete':
            if ($id > 0) {
                $model->eliminar($id);
                $feedback_message = "Precio eliminado exitosamente.";
            }
            break;
    }
} catch (Exception $e) {
    $feedback_message = "Error: " . $e->getMessage();
}

// Obtener datos para la vista
$items = $model->obtenerTodos();
$cursos = $model->obtenerCursos();
$tipos_precio = $model->obtenerTiposDePrecio();


$item_a_editar = null;
if ($action === 'edit' && $id > 0) {
    $item_a_editar = $model->obtenerPorId($id);
}

require_once 'views/lista_precios_view.php';
