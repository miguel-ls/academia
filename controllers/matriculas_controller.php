<?php

// =================================================================
// Controlador para la gestión de Matrículas
// =================================================================

require_once 'models/MatriculaModel.php';
require_once 'models/ClienteModel.php';
require_once 'models/MonitorModel.php';
require_once 'models/ProgramacionModel.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$matriculaModel = new MatriculaModel();
$clienteModel = new ClienteModel();
$monitorModel = new MonitorModel();
$programacionModel = new ProgramacionModel();


// Determinar la acción: puede venir por GET (navegación) o POST (formularios)
$action = $_REQUEST['action'] ?? 'list';


switch ($action) {
    case 'list':
        $matriculas = $matriculaModel->obtenerTodas();
        require_once 'views/matriculas_view.php';
        break;

    case 'nueva':
        // Cargar la vista principal del formulario
        require_once 'views/matricula_nueva_view.php';
        break;

    case 'buscar_cliente':
        // Endpoint para AJAX
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';
        $clientes = $clienteModel->buscar($query);
        echo json_encode($clientes);
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

                foreach ($_POST['cursos'] as $id_curso_programado => $curso) {
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
                foreach ($_POST['cursos'] as $id_curso_programado => $curso_data) {
                    $clientes_a_validar[$curso_data['id_cliente_asistencia']] = true;
                }

                foreach (array_keys($clientes_a_validar) as $id_cliente) {
                    // 1. Obtener los horarios activos existentes del cliente
                    $horarios_existentes = $matriculaModel->obtenerHorariosActivosPorCliente($id_cliente);

                    // 2. Preparar la lista de cursos nuevos para este cliente
                    $cursos_nuevos = [];
                    foreach ($_POST['cursos'] as $id_curso_programado => $curso_data) {
                        if ($curso_data['id_cliente_asistencia'] == $id_cliente) {
                            $programacion = $programaciones_cursos[$id_curso_programado];
                            $cursos_nuevos[] = [
                                'id_sub_area'   => $programacion['id_sub_area'],
                                'fecha_inicio'  => $curso_data['fecha_inicio'],
                                'fecha_fin'     => $curso_data['fecha_fin'],
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

                foreach ($_POST['cursos'] as $id_curso_programado => $curso) {
                    $monto_total += (float)$curso['precio_pactado'];
                    $descuento_total += (float)$curso['descuento'];
                    $cursos_detalle[] = [
                        'id_curso_programado' => (int)$id_curso_programado,
                        'id_cliente_asistencia' => (int)$curso['id_cliente_asistencia'],
                        'precio_pactado' => (float)$curso['precio_pactado'],
                        'descuento' => (float)$curso['descuento']
                    ];
                }
                $monto_final = $monto_total - $descuento_total;

                $datos_matricula = [
                    'id_cliente' => (int)$_POST['id_cliente'],
                    'id_forma_pago' => (int)$_POST['id_forma_pago'],
                    'fecha_inicio_matricula' => $_POST['fecha_inicio_matricula'],
                    'fecha_fin_matricula' => $_POST['fecha_fin_matricula'],
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
                // Redirigir de vuelta al formulario con un mensaje de error
                header('Location: index.php?view=matriculas&action=nueva&error=' . urlencode($e->getMessage()));
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
        require_once 'views/matriculas_view.php';
        break;
}
