<?php

// =================================================================
// Controlador para el Mantenimiento de Clientes
// =================================================================

require_once 'models/ClienteModel.php';

// --- Verificación de Seguridad ---
// Cualquier usuario logueado puede gestionar clientes.
Session::check();
// ---------------------------------

$clienteModel = new ClienteModel();

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = (int)($_POST['id_cliente'] ?? $_GET['id_cliente'] ?? 0);
$feedback_message = '';

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
                    'codigo_erp' => $_POST['codigo_erp']
                ];
                $clienteModel->crear($datos);
                $feedback_message = "Cliente creado exitosamente.";
            }
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                $datos = [
                    'id_cliente' => $id,
                    'id_tipo_documento' => $_POST['id_tipo_documento'],
                    'numero_documento' => $_POST['numero_documento'],
                    'nombres' => $_POST['nombres'],
                    'apellidos' => $_POST['apellidos'],
                    'email' => $_POST['email'],
                    'telefono' => $_POST['telefono'],
                    'codigo_erp' => $_POST['codigo_erp']
                ];
                $clienteModel->actualizar($datos);
                $feedback_message = "Cliente actualizado exitosamente.";
            }
            break;

        // No hay caso 'delete' por diseño.
    }
} catch (Exception $e) {
    $feedback_message = "Error: " . $e->getMessage();
}

// --- Obtención de datos para la vista ---

$clientes = $clienteModel->obtenerTodos();

$cliente_a_editar = null;
if ($action === 'edit' && $id > 0) {
    $cliente_a_editar = $clienteModel->obtenerPorId($id);
}

// Para el formulario, necesitamos la lista de tipos de documento
// En un modelo más grande, habría un TiposDocumentoModel.
// Por ahora, lo obtenemos con una consulta directa (a través de un SP genérico si existiera)
// o lo hardcodeamos temporalmente en la vista.

require_once 'views/clientes_view.php';
