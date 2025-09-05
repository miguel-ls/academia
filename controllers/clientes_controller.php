<?php

// =================================================================
// Controlador para el Mantenimiento de Clientes (Refactorizado v2)
// =================================================================

require_once 'models/ClienteModel.php';
require_once 'models/TiposDocumentoModel.php';

// --- NO LLAMAR A Session::check() globalmente ---
// Se llamará dentro de cada case que renderice una página completa.

$clienteModel = new ClienteModel();
$tiposDocumentoModel = new TiposDocumentoModel();

// --- Gestión de la Acción ---
$action = $_REQUEST['action'] ?? 'list'; // Usar $_REQUEST para aceptar GET y POST
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$clientes = [];
$cliente_a_editar = null;
$tipos_documento = [];
$search_term = '';


try {
    switch ($action) {
        case 'create':
            Session::check(); // Proteger la acción
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_documento' => $_POST['id_tipo_documento'],
                    'numero_documento' => $_POST['numero_documento'],
                    'nombres' => $_POST['nombres'],
                    'apellidos' => $_POST['apellidos'],
                    'email' => $_POST['email'],
                    'telefono' => $_POST['telefono'],
                    'codigo_erp' => $_POST['codigo_erp'],
                    'direccion' => $_POST['direccion'],
                    'codigo_ubigeo' => $_POST['codigo_ubigeo']
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
            header('Location: index.php?view=clientes&action=new');
            exit();

        case 'update':
            Session::check(); // Proteger la acción
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_cliente' => $_POST['id_cliente'],
                    'id_tipo_documento' => $_POST['id_tipo_documento'],
                    'numero_documento' => $_POST['numero_documento'],
                    'nombres' => $_POST['nombres'],
                    'apellidos' => $_POST['apellidos'],
                    'email' => $_POST['email'],
                    'telefono' => $_POST['telefono'],
                    'codigo_erp' => $_POST['codigo_erp'],
                    'direccion' => $_POST['direccion'],
                    'codigo_ubigeo' => $_POST['codigo_ubigeo']
                ];
                $resultado = $clienteModel->actualizar($datos);

                if ($resultado['success']) {
                    $_SESSION['feedback_message'] = "Cliente actualizado exitosamente.";
                    header('Location: index.php?view=clientes');
                    exit();
                } else {
                    if (isset($resultado['error']) && strpos($resultado['error'], 'El nuevo número de documento ya está en uso') !== false) {
                        $error_message = "Error: El número de documento '" . htmlspecialchars($datos['numero_documento']) . "' ya está registrado.";
                        $cliente_a_editar = $datos;
                        $tipos_documento = $tiposDocumentoModel->obtenerTodos();
                        require_once 'views/clientes/form.php';
                    } else {
                        $_SESSION['feedback_message'] = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                        header('Location: index.php?view=clientes');
                        exit();
                    }
                }
            } else {
                header('Location: index.php?view=clientes');
                exit();
            }
            break;

        case 'crear_ajax':
            // Proteger la acción AJAX con una comprobación que devuelve JSON
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401); // Unauthorized
                echo json_encode(['success' => false, 'error' => 'Sesión expirada. Por favor, inicie sesión de nuevo.']);
                exit();
            }
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_documento' => $_POST['id_tipo_documento'],
                    'numero_documento' => $_POST['numero_documento'],
                    'nombres' => $_POST['nombres'],
                    'apellidos' => $_POST['apellidos'],
                    'email' => $_POST['email'] ?? null,
                    'telefono' => $_POST['telefono'] ?? null,
                    'codigo_erp' => $_POST['codigo_erp'] ?? null,
                    'direccion' => $_POST['direccion'] ?? null,
                    'codigo_ubigeo' => $_POST['codigo_ubigeo'] ?? null
                ];

                if (empty($datos['nombres']) || empty($datos['numero_documento'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Nombres, apellidos y número de documento son obligatorios.']);
                    exit();
                }

                $resultado = $clienteModel->crear($datos);

                if ($resultado['success']) {
                    $nuevo_cliente = $clienteModel->obtenerPorId($resultado['id']);
                    echo json_encode(['success' => true, 'cliente' => $nuevo_cliente]);
                } else {
                    http_response_code(400);
                    echo json_encode(
                        ['success' => false, 'error' => 'Error al crear el cliente: ' . $resultado['error']],
                        JSON_INVALID_UTF8_SUBSTITUTE
                    );
                }
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
            }
            exit();

        case 'proxy_sunat':
            // Proteger la acción AJAX con una comprobación que devuelve JSON
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401); // Unauthorized
                echo json_encode(['error' => 'Sesión expirada. Por favor, inicie sesión de nuevo.']);
                exit();
            }
            header('Content-Type: application/json');

            $tipo = $_GET['tipo'] ?? '';
            $numero = $_GET['numero'] ?? '';

            if (empty($tipo) || empty($numero)) {
                http_response_code(400);
                echo json_encode(['error' => 'Tipo y número de documento son requeridos.']);
                exit();
            }

            $apiUrl = '';
            if ($tipo === 'DNI') {
                $apiUrl = "https://api.apis.net.pe/v1/dni?numero=" . urlencode($numero);
            } elseif ($tipo === 'RUC') {
                $apiUrl = "https://api.apis.net.pe/v1/ruc?numero=" . urlencode($numero);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Tipo de documento no válido.']);
                exit();
            }

            // Usar file_get_contents con un stream context para manejar errores y timeouts
            $options = [
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
                "http" => [
                    'timeout' => 5 // 5 segundos de timeout
                ]
            ];
            $context = stream_context_create($options);
            $response = @file_get_contents($apiUrl, false, $context);

            if ($response === FALSE) {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo conectar con el servicio de consulta.']);
                exit();
            }

            // Simplemente pasar la respuesta
            echo $response;
            exit();

        case 'check_documento':
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['exists' => false, 'error' => 'Sesión expirada.']);
                exit();
            }
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
            Session::check(); // Proteger la acción
            if ($id > 0) {
                $num_matriculas = $clienteModel->verificarMatriculas($id);
                if ($num_matriculas > 0) {
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar el cliente porque tiene {$num_matriculas} matrícula(s) asociada(s).";
                } else {
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
            Session::check(); // Proteger la vista
            $tipos_documento = $tiposDocumentoModel->obtenerTodos();
            require_once 'views/clientes/form.php';
            break;

        case 'edit':
            Session::check(); // Proteger la vista
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
            Session::check(); // Proteger la vista
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
    Session::check();
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    require_once 'views/clientes/list.php';
}
