<?php

// =================================================================
// Controlador para la gestión de Matrículas
// =================================================================

require_once 'models/MatriculaModel.php';
require_once 'models/ClienteModel.php';
require_once 'models/MonitorModel.php';
require_once 'models/ProgramacionModel.php';
require_once 'models/FormasPagoModel.php';
require_once 'models/CursosModel.php';
require_once 'models/SubAreasModel.php';
require_once 'models/ProfesorModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$matriculaModel = new MatriculaModel();
$clienteModel = new ClienteModel();
$monitorModel = new MonitorModel();
$programacionModel = new ProgramacionModel();
$formasPagoModel = new FormasPagoModel();
$cursosModel = new CursosModel();
$subAreasModel = new SubAreasModel();
$profesorModel = new ProfesorModel();


// Determinar la acción: puede venir por GET (navegación) o POST (formularios)
$action = $_REQUEST['action'] ?? 'list';

function validarFechaMatricula($fecha, $campo) {
    $date = DateTime::createFromFormat('Y-m-d', (string)$fecha);
    if (!$date || $date->format('Y-m-d') !== $fecha) {
        throw new Exception("La {$campo} no es válida.");
    }
    return $fecha;
}


switch ($action) {
    case 'list':
        $matriculas = $matriculaModel->obtenerTodas();
        $filtro_cliente_id = (int)($_GET['cliente_id'] ?? 0);
        $filtro_curso_id = (int)($_GET['curso_id'] ?? 0);
        $filtro_ubicacion_id = (int)($_GET['ubicacion_id'] ?? 0);
        $filtro_profesor_id = (int)($_GET['profesor_id'] ?? 0);
        $filtro_horario_id = (int)($_GET['horario_id'] ?? 0);
        $filtro_fecha_inicio = $_GET['fecha_inicio'] ?? '';
        $filtro_fecha_fin = $_GET['fecha_fin'] ?? '';
        $filtro_estado = $_GET['estado'] ?? '';

        $matriculas = array_values(array_filter($matriculas, function ($matricula) use ($filtro_cliente_id, $filtro_curso_id, $filtro_ubicacion_id, $filtro_profesor_id, $filtro_horario_id, $filtro_fecha_inicio, $filtro_fecha_fin, $filtro_estado) {
            $fecha_matricula = date('Y-m-d', strtotime($matricula['fecha_matricula']));
            $clientes_matricula = array_filter(explode(',', (string)($matricula['clientes_asistencia_ids'] ?? '')));
            $cursos_matricula = array_filter(explode(',', (string)($matricula['cursos_ids'] ?? '')));
            $ubicaciones_matricula = array_filter(explode(',', (string)($matricula['ubicaciones_ids'] ?? '')));
            $profesores_matricula = array_filter(explode(',', (string)($matricula['profesores_ids'] ?? '')));
            $horarios_matricula = array_filter(explode(',', (string)($matricula['horarios_ids'] ?? '')));
            return (!$filtro_cliente_id || in_array((string)$filtro_cliente_id, $clientes_matricula, true))
                && (!$filtro_curso_id || in_array((string)$filtro_curso_id, $cursos_matricula, true))
                && (!$filtro_ubicacion_id || in_array((string)$filtro_ubicacion_id, $ubicaciones_matricula, true))
                && (!$filtro_profesor_id || in_array((string)$filtro_profesor_id, $profesores_matricula, true))
                && (!$filtro_horario_id || in_array((string)$filtro_horario_id, $horarios_matricula, true))
                && (!$filtro_fecha_inicio || $fecha_matricula >= $filtro_fecha_inicio)
                && (!$filtro_fecha_fin || $fecha_matricula <= $filtro_fecha_fin)
                && (!$filtro_estado || $matricula['estado'] === $filtro_estado);
        }));
        require_once 'views/matriculas/list.php';
        break;

    case 'nueva':
        // Cargar datos adicionales para el formulario, como los tipos de documento
        require_once 'models/TiposDocumentoModel.php';
        $tiposDocumentoModel = new TiposDocumentoModel();
        $tipos_documento = $tiposDocumentoModel->obtenerTodos();
        $formas_pago = $formasPagoModel->obtenerTodos();

        // Cargar la vista principal del formulario
        require_once 'views/matriculas/nueva.php';
        break;

    case 'buscar_cliente':
        // Endpoint para AJAX
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';
        $clientes = $clienteModel->buscar($query);
        echo json_encode($clientes);
        exit;

    case 'buscar_curso_filtro':
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';
        echo json_encode($cursosModel->buscar($query));
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

    case 'buscar_cursos':
        // Endpoint para AJAX
        header('Content-Type: application/json');
        $filtros = [
            'id_profesor'   => !empty($_GET['profesor_id']) ? (int)$_GET['profesor_id'] : null,
            'fecha_inicio'  => !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null,
            'fecha_fin'     => !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null
        ];
        $cursos = $monitorModel->obtenerCursosDisponibles($filtros);
        echo json_encode($cursos);
        exit;

    case 'registrar_matricula':
        // Endpoint para el POST final del formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Recalcular totales en el backend por seguridad
                $monto_total = 0;
                $descuento_total = 0;
                $cursos_detalle = [];

                if(empty($_POST['cursos'])){
                     throw new Exception("Debe seleccionar al menos un curso.");
                }

                // --- Validación de Vacantes y Recopilación de Datos de Programación ---
                $nuevos_por_curso = [];
                $programaciones_cursos = []; // Almacenar datos de programación para reutilizar

                foreach ($_POST['cursos'] as $curso) {
                    $id_curso_programado = (int)($curso['id_curso_programado'] ?? 0);
                    if (!isset($nuevos_por_curso[$id_curso_programado])) {
                        $nuevos_por_curso[$id_curso_programado] = 0;
                    }
                    $nuevos_por_curso[$id_curso_programado]++;
                }

                foreach ($nuevos_por_curso as $id_curso_programado => $nuevos_alumnos) {
                    $programacion = $programacionModel->obtenerPorId($id_curso_programado);
                    if (!$programacion) {
                        throw new Exception("El curso programado con ID {$id_curso_programado} no existe.");
                    }
                    $programaciones_cursos[$id_curso_programado] = $programacion; // Guardar para después

                    $vacantes = (int)$programacion['vacantes'];
                    $inscritos = $matriculaModel->contarInscritosPorCursoProgramado($id_curso_programado);

                    if (($inscritos + $nuevos_alumnos) > $vacantes) {
                        $disponibles = $vacantes - $inscritos;
                        throw new Exception("No hay suficientes vacantes para el curso '{$programacion['nombre_curso']}'. Disponibles: {$disponibles}, Intentando inscribir: {$nuevos_alumnos}.");
                    }
                }
                // --- Fin Validación de Vacantes ---

                // --- Validación de Cruce de Horarios para Clientes (Mejorada) ---
                $clientes_a_validar = [];
                foreach ($_POST['cursos'] as $curso_data) {
                    $clientes_a_validar[$curso_data['id_cliente_asistencia']] = true;
                }

                foreach (array_keys($clientes_a_validar) as $id_cliente) {
                    // 1. Obtener los horarios activos existentes del cliente
                    $horarios_existentes = $matriculaModel->obtenerHorariosActivosPorCliente($id_cliente);

                    // 2. Preparar la lista de cursos nuevos para este cliente
                    $cursos_nuevos = [];
                    foreach ($_POST['cursos'] as $curso_data) {
                        $id_curso_programado = (int)($curso_data['id_curso_programado'] ?? 0);
                        if ($curso_data['id_cliente_asistencia'] == $id_cliente) {
                            $programacion = $programaciones_cursos[$id_curso_programado];
                            $cursos_nuevos[] = [
                                'id_sub_area'   => $programacion['id_sub_area'],
                                'fecha_inicio'  => $curso_data['fecha_inicio_clases'],
                                'fecha_fin'     => $curso_data['fecha_fin_clases'],
                                'hora_inicio'   => $curso_data['hora_inicio'],
                                'hora_fin'      => $curso_data['hora_fin'],
                                'dias_semana'   => $curso_data['dias_semana'],
                                'nombre_curso'  => $programacion['nombre_curso'] // Para mensajes de error
                            ];
                        }
                    }

                    // 3. Combinar horarios existentes y nuevos en una sola lista para la validación
                    $todos_los_horarios = array_merge($horarios_existentes, $cursos_nuevos);

                    // 4. Realizar la validación de cruce
                    if (count($todos_los_horarios) > 1) {
                        for ($i = 0; $i < count($todos_los_horarios); $i++) {
                            for ($j = $i + 1; $j < count($todos_los_horarios); $j++) {
                                $h1 = $todos_los_horarios[$i];
                                $h2 = $todos_los_horarios[$j];

                                if ($h1['id_sub_area'] != $h2['id_sub_area']) {
                                    continue;
                                }

                                $fechas_cruzadas = ($h1['fecha_inicio'] <= $h2['fecha_fin']) && ($h1['fecha_fin'] >= $h2['fecha_inicio']);
                                $horas_cruzadas = ($h1['hora_inicio'] < $h2['hora_fin']) && ($h1['hora_fin'] > $h2['hora_inicio']);
                                $dias1_arr = explode(',', $h1['dias_semana']);
                                $dias2_arr = explode(',', $h2['dias_semana']);
                                $dias_cruzados = count(array_intersect($dias1_arr, $dias2_arr)) > 0;

                                if ($fechas_cruzadas && $horas_cruzadas && $dias_cruzados) {
                                    $cliente_info = $clienteModel->obtenerPorId($id_cliente);
                                    $nombre_cliente = $cliente_info ? $cliente_info['nombres'] . ' ' . $cliente_info['apellidos'] : "ID {$id_cliente}";
                                    $curso1_nombre = $h1['nombre_curso'] ?? 'un curso existente';
                                    $curso2_nombre = $h2['nombre_curso'] ?? 'un curso existente';
                                    throw new Exception("Cruce de horario para {$nombre_cliente} entre '{$curso1_nombre}' y '{$curso2_nombre}'. Revise los cursos seleccionados y las matrículas activas del cliente.");
                                }
                            }
                        }
                    }
                }
                // --- Fin de Validación ---

                foreach ($_POST['cursos'] as $curso) {
                    $id_curso_programado = (int)($curso['id_curso_programado'] ?? 0);
                    $fecha_inicio_clases = validarFechaMatricula($curso['fecha_inicio_clases'] ?? '', 'fecha inicial de clases');
                    $fecha_fin_clases = validarFechaMatricula($curso['fecha_fin_clases'] ?? '', 'fecha final de clases');
                    if ($fecha_inicio_clases > $fecha_fin_clases) {
                        throw new Exception('La fecha inicial de clases no puede ser posterior a la fecha final.');
                    }
                    $monto_total += (float)$curso['precio_pactado'];
                    $descuento_total += (float)$curso['descuento'];
                    $cursos_detalle[] = [
                        'id_curso_programado' => (int)$id_curso_programado,
                        'id_cliente_asistencia' => (int)$curso['id_cliente_asistencia'],
                        'fecha_inicio_clases' => $fecha_inicio_clases,
                        'fecha_fin_clases' => $fecha_fin_clases,
                        'precio_pactado' => (float)$curso['precio_pactado'],
                        'descuento' => (float)$curso['descuento']
                    ];
                }
                $monto_final = $monto_total - $descuento_total;

                $datos_matricula = [
                    'id_cliente' => (int)$_POST['id_cliente'],
                    'id_forma_pago' => (int)$_POST['id_forma_pago'],
                    'fecha_matricula' => validarFechaMatricula($_POST['fecha_matricula'] ?? '', 'fecha de matrícula'),
                    'observaciones' => $_POST['observaciones'],
                    'monto_total' => $monto_total,
                    'descuento_total' => $descuento_total,
                    'monto_final' => $monto_final,
                    'cursos' => $cursos_detalle
                ];

                $matriculaModel->registrarMatricula($datos_matricula);

                // Redirigir a la lista con un mensaje de éxito
                header('Location: index.php?view=matriculas&success=1');
                exit;

            } catch (Exception $e) {
                // Volver a mostrar el formulario con todos los datos enviados.
                require_once 'models/TiposDocumentoModel.php';
                $tiposDocumentoModel = new TiposDocumentoModel();
                $tipos_documento = $tiposDocumentoModel->obtenerTodos();
                $formas_pago = $formasPagoModel->obtenerTodos();
                $error_message = $e->getMessage();
                $id_cliente = (int)($_POST['id_cliente'] ?? 0);
                $cliente_principal = $id_cliente > 0 ? $clienteModel->obtenerPorId($id_cliente) : null;
                $form_data = $_POST;
                $matriculaDetalles = [];

                foreach (($_POST['cursos'] ?? []) as $curso) {
                    $id_curso_programado = (int)($curso['id_curso_programado'] ?? 0);
                    $matriculaDetalles[] = [
                        'id_curso_programado' => $id_curso_programado,
                        'nombre_curso' => $curso['nombre_curso'] ?? '',
                        'ubicacion' => $curso['ubicacion'] ?? '',
                        'profesor' => $curso['profesor'] ?? '',
                        'horario_dias' => $curso['horario_dias'] ?? '',
                        'dias_semana' => $curso['dias_semana'] ?? '',
                        'fecha_inicio' => $curso['fecha_inicio'] ?? '',
                        'fecha_fin' => $curso['fecha_fin'] ?? '',
                        'fecha_inicio_clases' => $curso['fecha_inicio_clases'] ?? '',
                        'fecha_fin_clases' => $curso['fecha_fin_clases'] ?? '',
                        'hora_inicio' => $curso['hora_inicio'] ?? '',
                        'hora_fin' => $curso['hora_fin'] ?? '',
                        'id_cliente_asistencia' => (int)($curso['id_cliente_asistencia'] ?? 0),
                        'nombre_cliente_asistencia' => $curso['cliente_nombre'] ?? '',
                        'horas' => $curso['horas'] ?? '',
                        'precio_pactado' => (float)($curso['precio_pactado'] ?? 0),
                        'descuento' => (float)($curso['descuento'] ?? 0)
                    ];
                    $cliente_asistente = $clienteModel->obtenerPorId((int)($curso['id_cliente_asistencia'] ?? 0));
                    if ($cliente_asistente && empty($matriculaDetalles[array_key_last($matriculaDetalles)]['nombre_cliente_asistencia'])) {
                        $matriculaDetalles[array_key_last($matriculaDetalles)]['nombre_cliente_asistencia'] = trim($cliente_asistente['nombres'] . ' ' . $cliente_asistente['apellidos']);
                    }
                }

                require_once 'views/matriculas/nueva.php';
                exit;
            }
        }
        break;

    case 'revertir_anulacion':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_matricula = (int)$_POST['id_matricula'];
                if ($id_matricula > 0) {
                    $matriculaModel->revertirAnulacion($id_matricula);
                    header('Location: index.php?view=matriculas&success_revertir=1');
                    exit;
                } else {
                    throw new Exception("ID de matrícula no válido.");
                }
            } catch (Exception $e) {
                header('Location: index.php?view=matriculas&error_revertir=' . urlencode($e->getMessage()));
                exit;
            }
        }
        break;

    case 'eliminar_detalle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_matricula = (int)($_POST['id_matricula'] ?? 0);
                $id_matricula_detalle = (int)($_POST['id_matricula_detalle'] ?? 0);

                if ($id_matricula > 0 && $id_matricula_detalle > 0) {
                    $matriculaModel->eliminarDetalle($id_matricula_detalle, $id_matricula);
                    // Redirigir de vuelta a la página de detalle para ver el resultado
                    header('Location: index.php?view=matriculas&action=detalle&id=' . $id_matricula . '&success_detalle_eliminado=1');
                    exit;
                } else {
                    throw new Exception("IDs de matrícula o detalle no válidos.");
                }
            } catch (Exception $e) {
                $id_matricula = (int)($_POST['id_matricula'] ?? 0);
                $redirect_url = 'index.php?view=matriculas' . ($id_matricula > 0 ? '&action=detalle&id=' . $id_matricula : '');
                header('Location: ' . $redirect_url . '&error_detalle_eliminado=' . urlencode($e->getMessage()));
                exit;
            }
        }
        break;

    case 'detalle':
        $id_matricula = (int)($_GET['id'] ?? 0);
        if ($id_matricula > 0) {
            $matricula = $matriculaModel->obtenerCabeceraPorId($id_matricula);
            $detalles = $matriculaModel->obtenerDetallesPorIdMatricula($id_matricula);

            if ($matricula) {
                // Cargar la nueva vista de detalle
                require_once 'views/matriculas/detalle.php';
            } else {
                // Manejar caso de matrícula no encontrada
                header('Location: index.php?view=matriculas&error_not_found=1');
                exit;
            }
        } else {
            header('Location: index.php?view=matriculas');
            exit;
        }
        break;

    case 'editar':
        $id_matricula = (int)($_GET['id'] ?? 0);
        if ($id_matricula > 0) {
            $matricula = $matriculaModel->obtenerCabeceraPorId($id_matricula);
            $detalles = $matriculaModel->obtenerDetallesPorIdMatricula($id_matricula);

            if ($matricula) {
                $formas_pago = $formasPagoModel->obtenerTodos();
                // Cargar la vista de edición, que será una versión modificada de la de nueva matrícula
                require_once 'views/matriculas/editar.php';
            } else {
                header('Location: index.php?view=matriculas&error_not_found=1');
                exit;
            }
        } else {
            header('Location: index.php?view=matriculas');
            exit;
        }
        break;

    case 'actualizar_matricula':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_matricula = (int)($_POST['id_matricula'] ?? 0);
            try {
                if ($id_matricula === 0) {
                    throw new Exception("ID de matrícula no válido.");
                }

                if(empty($_POST['cursos'])){
                     throw new Exception("La matrícula no puede quedar sin cursos.");
                }

                // --- Validaciones (similar a registrar_matricula) ---
                // ... (La lógica de validación de vacantes y cruce de horarios se debe implementar aquí,
                // pero es más complejo porque hay que diferenciar cursos nuevos de existentes.
                // Esto se hará en el modelo para mantener el controlador limpio).

                // --- Recopilación de datos ---
                $monto_total = 0;
                $descuento_total = 0;
                $cursos_detalle = [];

                foreach ($_POST['cursos'] as $curso) {
                    $id_curso_programado = (int)($curso['id_curso_programado'] ?? 0);
                    $fecha_inicio_clases = validarFechaMatricula($curso['fecha_inicio_clases'] ?? '', 'fecha inicial de clases');
                    $fecha_fin_clases = validarFechaMatricula($curso['fecha_fin_clases'] ?? '', 'fecha final de clases');
                    if ($fecha_inicio_clases > $fecha_fin_clases) {
                        throw new Exception('La fecha inicial de clases no puede ser posterior a la fecha final.');
                    }
                    $monto_total += (float)$curso['precio_pactado'];
                    $descuento_total += (float)$curso['descuento'];
                    $cursos_detalle[] = [
                        'id_matricula_detalle' => (int)($curso['id_matricula_detalle'] ?? 0),
                        'id_curso_programado' => (int)$id_curso_programado,
                        'id_cliente_asistencia' => (int)$curso['id_cliente_asistencia'],
                        'fecha_inicio_clases' => $fecha_inicio_clases,
                        'fecha_fin_clases' => $fecha_fin_clases,
                        'precio_pactado' => (float)$curso['precio_pactado'],
                        'descuento' => (float)$curso['descuento']
                    ];
                }
                $monto_final = $monto_total - $descuento_total;

                $datos_matricula = [
                    'id_cliente' => (int)$_POST['id_cliente'],
                    'id_forma_pago' => (int)$_POST['id_forma_pago'],
                    'fecha_matricula' => validarFechaMatricula($_POST['fecha_matricula'] ?? '', 'fecha de matrícula'),
                    'observaciones' => $_POST['observaciones'],
                    'monto_total' => $monto_total,
                    'descuento_total' => $descuento_total,
                    'monto_final' => $monto_final,
                    'cursos' => $cursos_detalle
                ];

                $resultado_actualizacion = $matriculaModel->actualizarMatricula($id_matricula, $datos_matricula);
                $mensaje = '';
                if (!$resultado_actualizacion['cobro_generado'] && $resultado_actualizacion['saldo_pendiente'] > 0.0001) {
                    $mensaje = 'La matrícula se actualizó. El saldo pendiente debe cobrarse desde Gestión de Cobros.';
                }

                header('Location: index.php?view=matriculas&action=detalle&id=' . $id_matricula . '&success_update=1' . ($mensaje !== '' ? '&message=' . urlencode($mensaje) : ''));
                exit;

            } catch (Exception $e) {
                header('Location: index.php?view=matriculas&action=editar&id=' . $id_matricula . '&error=' . urlencode($e->getMessage()));
                exit;
            }
        }
        break;

    case 'eliminar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_matricula = (int)$_POST['id_matricula'];
                if ($id_matricula > 0) {
                    $matriculaModel->eliminar($id_matricula);
                    // Usar un parámetro de éxito diferente para mensajes distintos
                    header('Location: index.php?view=matriculas&success_eliminacion=1');
                    exit;
                } else {
                    throw new Exception("ID de matrícula no válido.");
                }
            } catch (Exception $e) {
                header('Location: index.php?view=matriculas&error_eliminacion=' . urlencode($e->getMessage()));
                exit;
            }
        }
        break;

    case 'anular':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_matricula = (int)$_POST['id_matricula'];
                $observaciones = $_POST['observaciones'];
                if ($id_matricula > 0) {
                    $matriculaModel->anular($id_matricula, $observaciones);
                    header('Location: index.php?view=matriculas&success_anulacion=1');
                    exit;
                }
            } catch (Exception $e) {
                header('Location: index.php?view=matriculas&error_anulacion=' . urlencode($e->getMessage()));
                exit;
            }
        }
        break;

    default:
        $matriculas = $matriculaModel->obtenerTodas();
        require_once 'views/matriculas/list.php';
        break;
}
