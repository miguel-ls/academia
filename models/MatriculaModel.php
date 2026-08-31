<?php

// =================================================================
// Modelo Matricula: Gestiona las matrículas de los clientes.
// =================================================================

class MatriculaModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene la lista de todas las matrículas.
     */
    public function obtenerTodas() {
        $this->db->callStoredProcedure('sp_matriculas_listar');
        return $this->db->resultSet();
    }

    public function buscarHorarios($termino) {
        $this->db->callStoredProcedure('sp_matriculas_horarios_buscar', [$termino]);
        return $this->db->resultSet();
    }

    /**
     * Registra una matrícula completa con su detalle en una transacción.
     * @param array $datos Cabecera y detalle de la matrícula.
     * @return bool True si fue exitoso.
     */
    public function registrarMatricula($datos) {
        $this->db->beginTransaction();

        try {
            $id_forma_pago = (int)($datos['id_forma_pago'] ?? 0);
            $fechas_inicio = array_column($datos['cursos'], 'fecha_inicio_clases');
            $fechas_fin = array_column($datos['cursos'], 'fecha_fin_clases');
            // 1. Registrar cabecera
            $params_cabecera = [
                $datos['id_cliente'],
                $_SESSION['user_id'], // El usuario que registra
                $id_forma_pago > 0 ? $id_forma_pago : null,
                $datos['fecha_matricula'],
                min($fechas_inicio),
                max($fechas_fin),
                $datos['monto_total'],
                $datos['descuento_total'],
                $datos['monto_final'],
                $datos['observaciones']
            ];
            $stmt_cabecera = $this->db->callStoredProcedure('sp_matricula_registrar_cabecera', $params_cabecera);
            $result_cabecera = $this->db->single();
            $id_matricula = $result_cabecera['id_matricula'] ?? 0;

            if ($id_matricula == 0) {
                throw new Exception("No se pudo crear la cabecera de la matrícula.");
            }

            // 2. Registrar detalles
            foreach ($datos['cursos'] as $curso_detalle) {
                $precio_final = (float)$curso_detalle['precio_pactado'] - (float)$curso_detalle['descuento'];
                $params_detalle = [
                    $id_matricula,
                    $curso_detalle['id_curso_programado'],
                    $curso_detalle['id_cliente_asistencia'],
                    $curso_detalle['precio_pactado'],
                    $curso_detalle['descuento'],
                    $precio_final,
                    $curso_detalle['fecha_inicio_clases'],
                    $curso_detalle['fecha_fin_clases']
                ];
                $stmt_detalle = $this->db->callStoredProcedure('sp_matricula_registrar_detalle', $params_detalle);
                $result_detalle = $this->db->single();
                $id_matricula_detalle = $result_detalle['id_matricula_detalle'] ?? 0;

                if ($id_matricula_detalle == 0) {
                    // El SP lanza un error si no hay vacantes, pero validamos por si acaso.
                    throw new Exception("No se pudo registrar el detalle para el curso ID " . $curso_detalle['id_curso_programado']);
                }

                // 3. Generar cronograma de asistencia para el cliente
                $this->db->callStoredProcedure('sp_asistencia_cliente_generar_cronograma', [$id_matricula_detalle]);
            }

            if ($id_forma_pago > 0) {
                $this->db->callStoredProcedure('sp_cobros_crear', [
                    $id_matricula,
                    $id_forma_pago,
                    date('Y-m-d'),
                    'AUTO-' . $id_matricula,
                    $datos['monto_final'],
                    'Cobro automático generado al registrar la matrícula.',
                    $_SESSION['user_id'] ?? 0,
                ]);
            }

            // 4. Si todo fue bien, confirmar la transacción
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // 5. Si algo falló, revertir la transacción
            $this->db->rollBack();
            // Propagar la excepción para que el controlador la maneje
            throw $e;
        }
    }

    /**
     * Actualiza una matrícula existente, incluyendo sus detalles.
     * @param int $id_matricula El ID de la matrícula a actualizar.
     * @param array $datos Los nuevos datos de la matrícula.
     * @return bool True si fue exitoso.
     */
    public function actualizarMatricula($id_matricula, $datos) {
        $matricula_actual = $this->obtenerCabeceraPorId($id_matricula);
        if (!$matricula_actual) {
            throw new Exception('No se encontró la matrícula a actualizar.');
        }

        $saldo_actual = $this->obtenerSaldoCobros($id_matricula);
        $total_cobros = (float)$matricula_actual['monto_final'] - $saldo_actual;
        if ((float)$datos['monto_final'] + 0.0001 < $total_cobros) {
            throw new Exception('No se puede grabar la matrícula porque el importe es menor a los cobros asociados.');
        }

        $this->db->beginTransaction();
        try {
            $id_forma_pago = (int)($datos['id_forma_pago'] ?? 0);
            // 1. Obtener el estado actual de la matrícula desde la BD
            $detalles_actuales_raw = $this->obtenerDetallesPorIdMatricula($id_matricula);
            $detalles_actuales = [];
            foreach ($detalles_actuales_raw as $detalle) {
                $detalles_actuales[$detalle['id_matricula_detalle']] = $detalle;
            }

            $cursos_enviados = [];
            foreach ($datos['cursos'] as $curso) {
                $clave = !empty($curso['id_matricula_detalle'])
                    ? (int)$curso['id_matricula_detalle']
                    : 'nuevo_' . count($cursos_enviados);
                $cursos_enviados[$clave] = $curso;
            }

            // 2. Determinar qué hacer con cada detalle (Añadir, Actualizar, Eliminar)
            $a_eliminar = array_diff_key($detalles_actuales, $cursos_enviados);
            $a_anadir = array_diff_key($cursos_enviados, $detalles_actuales);
            $a_actualizar = array_intersect_key($cursos_enviados, $detalles_actuales);

            // 3. Procesar eliminaciones
            foreach ($a_eliminar as $detalle_a_eliminar) {
                $this->eliminarDetalle($detalle_a_eliminar['id_matricula_detalle'], $id_matricula);
            }

            // 4. Procesar adiciones
            foreach ($a_anadir as $detalle_a_anadir) {
                $precio_final = (float)$detalle_a_anadir['precio_pactado'] - (float)$detalle_a_anadir['descuento'];
                $params = [
                    $id_matricula,
                    $detalle_a_anadir['id_curso_programado'],
                    $detalle_a_anadir['id_cliente_asistencia'],
                    $detalle_a_anadir['precio_pactado'],
                    $detalle_a_anadir['descuento'],
                    $precio_final,
                    $detalle_a_anadir['fecha_inicio_clases'],
                    $detalle_a_anadir['fecha_fin_clases']
                ];
                $this->db->callStoredProcedure('sp_matricula_registrar_detalle', $params);
                $result = $this->db->single();
                $id_nuevo_detalle = $result['id_matricula_detalle'] ?? 0;
                if ($id_nuevo_detalle > 0) {
                    $this->db->callStoredProcedure('sp_asistencia_cliente_generar_cronograma', [$id_nuevo_detalle]);
                }
            }

            // 5. Procesar actualizaciones
            foreach ($a_actualizar as $id_matricula_detalle => $detalle_a_actualizar) {
                $detalle_existente = $detalles_actuales[$id_matricula_detalle];
                if ($detalle_existente['id_cliente_asistencia'] != $detalle_a_actualizar['id_cliente_asistencia'] ||
                    (float)$detalle_existente['precio_pactado'] != (float)$detalle_a_actualizar['precio_pactado'] ||
                    (float)$detalle_existente['descuento'] != (float)$detalle_a_actualizar['descuento'] ||
                    $detalle_existente['fecha_inicio_clases'] != $detalle_a_actualizar['fecha_inicio_clases'] ||
                    $detalle_existente['fecha_fin_clases'] != $detalle_a_actualizar['fecha_fin_clases']) {

                    $params_detalle = [
                        $detalle_existente['id_matricula_detalle'],
                        $detalle_a_actualizar['id_cliente_asistencia'],
                        $detalle_a_actualizar['precio_pactado'],
                        $detalle_a_actualizar['descuento'],
                        $detalle_a_actualizar['fecha_inicio_clases'],
                        $detalle_a_actualizar['fecha_fin_clases']
                    ];
                    $this->db->callStoredProcedure('sp_matricula_detalle_actualizar', $params_detalle);
                }
            }

            // 6. Actualizar la cabecera de la matrícula
            $params_cabecera = [
                $id_matricula,
                $id_forma_pago > 0 ? $id_forma_pago : null,
                $datos['fecha_matricula'],
                $datos['observaciones']
            ];
            $this->db->callStoredProcedure('sp_matricula_cabecera_actualizar', $params_cabecera);

            // 7. Recalcular totales finales
            $this->db->callStoredProcedure('sp_matricula_cabecera_recalcular', [$id_matricula]);

            if ($total_cobros <= 0.0001 && $id_forma_pago > 0) {
                $this->db->callStoredProcedure('sp_cobros_crear', [
                    $id_matricula,
                    $id_forma_pago,
                    date('Y-m-d'),
                    'AUTO-' . $id_matricula,
                    $datos['monto_final'],
                    '',
                    $_SESSION['user_id'] ?? 0,
                ]);
            }

            $this->db->commit();
            return [
                'cobro_generado' => $total_cobros <= 0.0001 && $id_forma_pago > 0,
                'saldo_pendiente' => max(0, (float)$datos['monto_final'] - $total_cobros),
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Anula una matrícula.
     * @param int $id_matricula
     * @param string $observaciones
     * @return bool
     */
    public function anular($id_matricula, $observaciones) {
        try {
            $this->db->callStoredProcedure('sp_matricula_anular', [$id_matricula, $observaciones]);
            return true;
        } catch (Exception $e) {
            // El SP puede lanzar un error si la matrícula no está activa, lo capturamos.
            throw new Exception("Error al anular la matrícula: " . $e->getMessage());
        }
    }

    /**
     * Reverte la anulación de una matrícula, volviéndola al estado 'Activa'.
     * @param int $id_matricula
     * @return bool
     */
    public function revertirAnulacion($id_matricula) {
        try {
            $this->db->callStoredProcedure('sp_matricula_revertir_anulacion', [$id_matricula]);
            return true;
        } catch (Exception $e) {
            // El SP lanzará un error si no hay vacantes o si la matrícula no está anulada.
            throw new Exception("Error al revertir la anulación: " . $e->getMessage());
        }
    }

    /**
     * Cuenta el número de alumnos inscritos en un curso programado específico.
     * @param int $id_curso_programado
     * @return int El número de alumnos inscritos.
     */
    public function contarInscritosPorCursoProgramado($id_curso_programado) {
        $this->db->callStoredProcedure('sp_matriculas_contar_por_curso', [$id_curso_programado]);
        $result = $this->db->single();
        return (int)($result['inscritos'] ?? 0);
    }

    /**
     * Obtiene todos los horarios activos para un cliente específico.
     * @param int $id_cliente
     * @return array Lista de horarios activos.
     */
    public function obtenerHorariosActivosPorCliente($id_cliente) {
        $this->db->callStoredProcedure('sp_cliente_horarios_activos', [$id_cliente]);
        return $this->db->resultSet();
    }

    /**
     * Elimina permanentemente una matrícula y todos sus registros asociados.
     * @param int $id_matricula
     * @return bool
     */
    public function eliminar($id_matricula) {
        try {
            $this->db->callStoredProcedure('sp_matricula_eliminar', [$id_matricula]);
            return true;
        } catch (Exception $e) {
            // En caso de un error de base de datos, propagar la excepción.
            throw new Exception("Error al eliminar la matrícula: " . $e->getMessage());
        }
    }

    /**
     * Obtiene la cabecera de una matrícula por su ID.
     * @param int $id_matricula
     * @return array|false
     */
    public function obtenerCabeceraPorId($id_matricula) {
        $this->db->callStoredProcedure('sp_matricula_obtener_cabecera_por_id', [$id_matricula]);
        return $this->db->single();
    }

    /**
     * Obtiene todos los detalles (cursos) de una matrícula.
     * @param int $id_matricula
     * @return array
     */
    public function obtenerDetallesPorIdMatricula($id_matricula) {
        $this->db->callStoredProcedure('sp_matricula_obtener_detalles_por_id_matricula', [$id_matricula]);
        return $this->db->resultSet();
    }

    private function obtenerSaldoCobros($id_matricula) {
        $this->db->callStoredProcedure('sp_cobros_saldo_matricula', [$id_matricula, 0]);
        $resultado = $this->db->single();
        return (float)($resultado['saldo_pendiente'] ?? 0.00);
    }

    /**
     * Elimina un curso (detalle) de una matrícula y recalcula los totales.
     * @param int $id_matricula_detalle
     * @param int $id_matricula
     * @return bool
     */
    public function eliminarDetalle($id_matricula_detalle, $id_matricula) {
        // La transacción es manejada por el método que llama (ej. actualizarMatricula)
        try {
            // 1. Eliminar el detalle y su asistencia
            $this->db->callStoredProcedure('sp_matricula_detalle_eliminar', [$id_matricula_detalle]);

            // 2. Recalcular los totales de la cabecera
            $this->db->callStoredProcedure('sp_matricula_cabecera_recalcular', [$id_matricula]);

            return true;
        } catch (Exception $e) {
            // Propagar la excepción para que la transacción principal haga rollback.
            throw new Exception("Error al eliminar el detalle de la matrícula: " . $e->getMessage());
        }
    }
}
