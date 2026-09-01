<?php

require_once 'models/CobroModel.php';
require_once 'models/ClienteModel.php';
require_once 'models/MatriculaModel.php';
require_once 'models/FormasPagoModel.php';
require_once 'models/CursosModel.php';
require_once 'models/SubAreasModel.php';
require_once 'models/ProfesorModel.php';

Session::check();

$cobroModel = new CobroModel();
$clienteModel = new ClienteModel();
$matriculaModel = new MatriculaModel();
$formasPagoModel = new FormasPagoModel();
$cursosModel = new CursosModel();
$subAreasModel = new SubAreasModel();
$profesorModel = new ProfesorModel();

$action = $_REQUEST['action'] ?? 'list';
$id_cobro = (int)($_REQUEST['id'] ?? 0);

$feedback_message = $_SESSION['feedback_message'] ?? '';
unset($_SESSION['feedback_message']);
$error_message = '';
$clientes_filtro = $clienteModel->obtenerTodos();
$matriculas_pendientes = [];
$formas_pago = $formasPagoModel->obtenerTodos();
$selected_matricula = null;
$saldo_pendiente = 0.00;
$cobro_actual = null;

try {
    switch ($action) {
        case 'buscar_pendientes':
            header('Content-Type: application/json');
            $term = $_GET['q'] ?? '';
            echo json_encode($cobroModel->buscarMatriculasPendientes($term));
            exit;

        case 'buscar_cliente_filtro':
            header('Content-Type: application/json');
            echo json_encode($clienteModel->buscar($_GET['q'] ?? ''));
            exit;

        case 'buscar_curso_filtro':
            header('Content-Type: application/json');
            echo json_encode($cursosModel->buscar($_GET['q'] ?? ''));
            exit;

        case 'buscar_ubicacion_filtro':
            header('Content-Type: application/json');
            echo json_encode($subAreasModel->buscar($_GET['q'] ?? ''));
            exit;

        case 'buscar_profesor_filtro':
            header('Content-Type: application/json');
            echo json_encode($profesorModel->buscar($_GET['q'] ?? ''));
            exit;

        case 'buscar_horario_filtro':
            header('Content-Type: application/json');
            echo json_encode($matriculaModel->buscarHorarios($_GET['q'] ?? ''));
            exit;

        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id_matricula = (int)($_POST['id_matricula'] ?? 0);
                $id_forma_pago = (int)($_POST['id_forma_pago'] ?? 0);
                $fecha_cobro = trim($_POST['fecha_cobro'] ?? '');
                $numero_operacion = trim((string)($_POST['numero_operacion'] ?? ''));
                $importe = (float)($_POST['importe'] ?? 0);
                $observaciones = trim((string)($_POST['observaciones'] ?? ''));

                if ($id_matricula <= 0) {
                    throw new Exception('Debe seleccionar una matrícula pendiente.');
                }
                if ($id_forma_pago <= 0) {
                    throw new Exception('Debe seleccionar una forma de pago.');
                }
                if ($fecha_cobro === '') {
                    throw new Exception('La fecha de cobro es obligatoria.');
                }
                if (strlen($numero_operacion) > 20) {
                    throw new Exception('El número de operación no puede superar 20 caracteres.');
                }
                if ($importe <= 0) {
                    throw new Exception('El importe debe ser mayor a cero.');
                }

                $saldo_actual = $cobroModel->obtenerSaldoMatricula($id_matricula);
                if ($importe > $saldo_actual + 0.0001) {
                    throw new Exception('Importe no válido o no permitido: el cobro no puede exceder el importe total de la matrícula.');
                }

                $datos = [
                    'id_matricula' => $id_matricula,
                    'id_forma_pago' => $id_forma_pago,
                    'fecha_cobro' => $fecha_cobro,
                    'numero_operacion' => $numero_operacion,
                    'importe' => number_format($importe, 2, '.', ''),
                    'observaciones' => $observaciones,
                ];

                $cobroModel->crear($datos);
                $_SESSION['feedback_message'] = 'Cobro registrado correctamente.';
                header('Location: index.php?view=cobros');
                exit;
            }

            $matriculas_pendientes = $cobroModel->obtenerMatriculasPendientes();
            require_once 'views/cobros/form.php';
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?view=cobros');
                exit;
            }

            $id_cobro = (int)($_POST['id_cobro'] ?? 0);
            $id_matricula = (int)($_POST['id_matricula'] ?? 0);
            $id_forma_pago = (int)($_POST['id_forma_pago'] ?? 0);
            $fecha_cobro = trim($_POST['fecha_cobro'] ?? '');
            $numero_operacion = trim((string)($_POST['numero_operacion'] ?? ''));
            $importe = (float)($_POST['importe'] ?? 0);
            $observaciones = trim((string)($_POST['observaciones'] ?? ''));

            if ($id_cobro <= 0) {
                throw new Exception('No se encontró el cobro seleccionado.');
            }
            if ($id_matricula <= 0) {
                throw new Exception('Debe seleccionar una matrícula válida.');
            }
            if ($id_forma_pago <= 0) {
                throw new Exception('Debe seleccionar una forma de pago.');
            }
            if ($fecha_cobro === '') {
                throw new Exception('La fecha de cobro es obligatoria.');
            }
            if (strlen($numero_operacion) > 20) {
                throw new Exception('El número de operación no puede superar 20 caracteres.');
            }
            if ($importe <= 0) {
                throw new Exception('El importe debe ser mayor a cero.');
            }

            $cobro_actual = $cobroModel->obtenerPorId($id_cobro);
            $saldo_actual = $cobroModel->obtenerSaldoMatricula($id_matricula, $id_cobro);
            if ($importe > $saldo_actual + 0.0001) {
                throw new Exception('Importe no válido o no permitido: el cobro no puede exceder el importe total de la matrícula.');
            }

            $datos = [
                'id_cobro' => $id_cobro,
                'id_forma_pago' => $id_forma_pago,
                'fecha_cobro' => $fecha_cobro,
                'numero_operacion' => $numero_operacion,
                'importe' => number_format($importe, 2, '.', ''),
                'observaciones' => $observaciones,
            ];

            $cobroModel->actualizar($datos);
            $_SESSION['feedback_message'] = 'Cobro actualizado correctamente.';
            header('Location: index.php?view=cobros');
            exit;

        case 'delete':
            if ($id_cobro > 0) {
                $cobroModel->eliminar($id_cobro);
                $_SESSION['feedback_message'] = 'Cobro eliminado correctamente.';
            } else {
                $_SESSION['feedback_message'] = 'Error: ID de cobro no válido.';
            }
            header('Location: index.php?view=cobros');
            exit;

        case 'ver':
            if ($id_cobro > 0) {
                $cobro_actual = $cobroModel->obtenerPorId($id_cobro);
                if (!$cobro_actual) {
                    $_SESSION['feedback_message'] = 'Error: Cobro no encontrado.';
                    header('Location: index.php?view=cobros');
                    exit;
                }
                $cobro_actual['saldo_pendiente'] = $cobroModel->obtenerSaldoMatricula($cobro_actual['id_matricula']);
                $selected_matricula = [
                    'id_matricula' => $cobro_actual['id_matricula'],
                    'nombre_cliente' => $cobro_actual['nombre_cliente'],
                    'alumnos_cursos' => $cobro_actual['alumnos_cursos'] ?? '',
                    'monto_final' => $cobro_actual['monto_final'],
                    'saldo_pendiente' => $cobro_actual['saldo_pendiente'],
                ];
                $saldo_pendiente = (float)($cobro_actual['saldo_pendiente'] ?? 0.00);
                $matriculas_pendientes = $cobroModel->obtenerMatriculasPendientes();
                require_once 'views/cobros/form.php';
            } else {
                $_SESSION['feedback_message'] = 'Error: ID de cobro no válido.';
                header('Location: index.php?view=cobros');
                exit;
            }
            break;

        case 'edit':
            if ($id_cobro > 0) {
                $cobro_actual = $cobroModel->obtenerPorId($id_cobro);
                if (!$cobro_actual) {
                    $_SESSION['feedback_message'] = 'Error: Cobro no encontrado.';
                    header('Location: index.php?view=cobros');
                    exit;
                }
                $cobro_actual['saldo_pendiente'] = $cobroModel->obtenerSaldoMatricula($cobro_actual['id_matricula']);
                $selected_matricula = [
                    'id_matricula' => $cobro_actual['id_matricula'],
                    'nombre_cliente' => $cobro_actual['nombre_cliente'],
                    'alumnos_cursos' => $cobro_actual['alumnos_cursos'] ?? '',
                    'monto_final' => $cobro_actual['monto_final'],
                    'saldo_pendiente' => $cobro_actual['saldo_pendiente'],
                ];
                $saldo_pendiente = (float)($cobro_actual['saldo_pendiente'] ?? 0.00);
                $matriculas_pendientes = $cobroModel->obtenerMatriculasPendientes();
                require_once 'views/cobros/form.php';
            } else {
                $_SESSION['feedback_message'] = 'Error: ID de cobro no válido.';
                header('Location: index.php?view=cobros');
                exit;
            }
            break;

        case 'new':
            $matriculas_pendientes = $cobroModel->obtenerMatriculasPendientes();
            require_once 'views/cobros/form.php';
            break;

        case 'list':
        default:
            $filtros = [
                'id_cliente' => 0,
                'fecha_cobro' => $_GET['fecha_cobro'] ?? '',
                'id_matricula' => (int)($_GET['numero_matricula'] ?? 0),
                'numero_operacion' => trim((string)($_GET['numero_operacion'] ?? '')),
            ];
            $cobros = $cobroModel->obtenerTodos($filtros);
            $filtros_detalle = [
                'cliente_id' => (int)($_GET['cliente_id'] ?? 0),
                'curso_id' => (int)($_GET['curso_id'] ?? 0),
                'ubicacion_id' => (int)($_GET['ubicacion_id'] ?? 0),
                'profesor_id' => (int)($_GET['profesor_id'] ?? 0),
                'horario_id' => (int)($_GET['horario_id'] ?? 0),
            ];
            $cobros = array_values(array_filter($cobros, function ($cobro) use ($filtros_detalle) {
                $campos = [
                    'cliente_id' => 'clientes_asistencia_ids',
                    'curso_id' => 'cursos_ids',
                    'ubicacion_id' => 'ubicaciones_ids',
                    'profesor_id' => 'profesores_ids',
                    'horario_id' => 'horarios_ids',
                ];
                foreach ($campos as $filtro => $campo) {
                    if ($filtros_detalle[$filtro] > 0 && !in_array((string)$filtros_detalle[$filtro], array_filter(explode(',', (string)($cobro[$campo] ?? ''))), true)) {
                        return false;
                    }
                }
                return true;
            }));
            require_once 'views/cobros/list.php';
            break;
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
    if ($action === 'create' || $action === 'update' || $action === 'new' || $action === 'edit' || $action === 'ver') {
        $matriculas_pendientes = $cobroModel->obtenerMatriculasPendientes();
        $formas_pago = $formasPagoModel->obtenerTodos();
        $id_matricula_enviada = (int)($_POST['id_matricula'] ?? 0);
        if ($id_matricula_enviada > 0) {
            foreach ($matriculas_pendientes as $matricula) {
                if ((int)$matricula['id_matricula'] === $id_matricula_enviada) {
                    $selected_matricula = $matricula;
                    $saldo_pendiente = (float)($matricula['saldo_pendiente'] ?? 0.00);
                    break;
                }
            }
        }
        require_once 'views/cobros/form.php';
    } else {
        $cobros = $cobroModel->obtenerTodos();
        require_once 'views/cobros/list.php';
    }
}
