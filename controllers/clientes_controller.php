<?php

// =================================================================
// Controlador para el Mantenimiento de Clientes (Refactorizado)
// =================================================================

require_once 'models/ClienteModel.php';
require_once 'models/TiposDocumentoModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$clienteModel = new ClienteModel();
$tiposDocumentoModel = new TiposDocumentoModel();

// --- Gestión de la Acción ---
// Si no se especifica, la acción por defecto es 'list'
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']); // Limpiar mensaje para no mostrarlo de nuevo

$error_message = '';
$clientes = [];
$cliente_a_editar = null;
$tipos_documento = [];
$search_term = '';


try {
    switch ($action) {
        case 'create':
            // Procesa el formulario de creación
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
                $resultado = $clienteModel->crear($datos);
                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Cliente creado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el cliente: " . $resultado['error'];
                }
                header('Location: index.php?view=clientes');
                exit();
            }
            // 'new' es el caso que muestra el formulario, no 'create'
            header('Location: index.php?view=clientes&action=new');
            exit();

        case 'update':
            // Procesa el formulario de edición (v2 con manejo de errores en la vista)
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_cliente' => $_POST['id_cliente'],
                    'id_tipo_documento' => $_POST['id_tipo_documento'],
                    'numero_documento' => $_POST['numero_documento'],
                    'nombres' => $_POST['nombres'],
                    'apellidos' => $_POST['apellidos'],
                    'email' => $_POST['email'],
                    'telefono' => $_POST['telefono'],
                    'codigo_erp' => $_POST['codigo_erp']
                ];
                $resultado = $clienteModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Cliente actualizado exitosamente.";
                    header('Location: index.php?view=clientes');
                    exit();
                } else {
                    // Verificar si el error es por documento duplicado
                    if (isset($resultado['error']) && strpos($resultado['error'], 'El nuevo número de documento ya está en uso') !== false) {
                        $error_message = "Error: El número de documento '" . htmlspecialchars($datos['numero_documento']) . "' ya está registrado.";

                        // Recargar los datos del formulario con lo que el usuario envió
                        $cliente_a_editar = $datos;

                        // Cargar los tipos de documento para el select
                        $tipos_documento = $tiposDocumentoModel->obtenerTodos();

                        // Volver a mostrar el formulario de edición con el error
                        require_once 'views/clientes/form.php';
                    } else {
                        // Otro tipo de error, redirigir con mensaje genérico
                        $_SESSION['feedback_message'] = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                        header('Location: index.php?view=clientes');
                        exit();
                    }
                }
            } else {
                // Si no es POST, redirigir a la lista
                header('Location: index.php?view=clientes');
                exit();
            }
            break;

        case 'crear_ajax':
            // Endpoint para creación de cliente vía AJAX desde la página de matrícula
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_documento' => $_POST['id_tipo_documento'],
                    'numero_documento' => $_POST['numero_documento'],
                    'nombres' => $_POST['nombres'],
                    'apellidos' => $_POST['apellidos'],
                    'email' => $_POST['email'] ?? null,
                    'telefono' => $_POST['telefono'] ?? null,
                    'codigo_erp' => $_POST['codigo_erp'] ?? null
                ];

                // Validaciones básicas del lado del servidor
                if (empty($datos['nombres']) || empty($datos['apellidos']) || empty($datos['numero_documento'])) {
                    echo json_encode(['success' => false, 'error' => 'Nombres, apellidos y número de documento son obligatorios.']);
                    exit();
                }

                $resultado = $clienteModel->crear($datos);

                if ($resultado['success']) {
                    $nuevo_cliente = $clienteModel->obtenerPorId($resultado['id']);
                    echo json_encode(['success' => true, 'cliente' => $nuevo_cliente]);
                } else {
                    // Set a Bad Request status code to be more explicit
                    http_response_code(400);
                    // Ensure the error message is properly encoded to prevent json_encode failure
                    $error_message = mb_convert_encoding($resultado['error'], 'UTF-8', 'UTF-8');
                    echo json_encode(['success' => false, 'error' => 'Error al crear el cliente: ' . $error_message]);
                }
            } else {
                http_response_code(405); // Method Not Allowed
                echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
            }
            exit();

        case 'check_documento':
            // Endpoint para validación AJAX
            header('Content-Type: application/json');
            $num_doc = $_GET['numero_documento'] ?? '';
            $id_excluir = !empty($_GET['id_cliente']) ? (int)$_GET['id_cliente'] : null;

            if (empty($num_doc)) {
                echo json_encode(['exists' => false]);
                exit();
            }

            $exists = $clienteModel->verificarDocumentoExistente($num_doc, $id_excluir);
            echo json_encode(['exists' => $exists]);
            exit();

        case 'delete':
            if ($id > 0) {
                // 1. Verificar si el cliente tiene matrículas
                $num_matriculas = $clienteModel->verificarMatriculas($id);

                if ($num_matriculas > 0) {
                    // 2. Si tiene, mostrar error y no eliminar
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar el cliente porque tiene {$num_matriculas} matrícula(s) asociada(s).";
                } else {
                    // 3. Si no tiene, proceder a eliminar
                    if ($clienteModel->eliminar($id)) {
                        $_SESSION['feedback_message'] = "Cliente eliminado exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo eliminar el cliente.";
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de cliente no válido.";
            }
            header('Location: index.php?view=clientes');
            exit();

        case 'new':
            // Muestra el formulario de creación
            $tipos_documento = $tiposDocumentoModel->obtenerTodos();
            require_once 'views/clientes/form.php';
            break;

        case 'edit':
            // Muestra el formulario de edición
            if ($id > 0) {
                $cliente_a_editar = $clienteModel->obtenerPorId($id);
                $tipos_documento = $tiposDocumentoModel->obtenerTodos();
                if (!$cliente_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Cliente no encontrado.";
                    header('Location: index.php?view=clientes');
                    exit();
                }
                require_once 'views/clientes/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de cliente no válido.";
                 header('Location: index.php?view=clientes');
                 exit();
            }
            break;

        case 'list':
        default:
            // Muestra la lista de clientes (con o sin búsqueda)
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $clientes = $clienteModel->buscar($search_term);
            } else {
                $clientes = $clienteModel->obtenerTodos();
            }
            require_once 'views/clientes/list.php';
            break;
    }

} catch (Exception $e) {
    // Captura de errores inesperados
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    // En un caso real, esto se loguearía y se mostraría una vista de error genérica
    require_once 'views/clientes/list.php'; // Volver a la lista con un mensaje de error
}
