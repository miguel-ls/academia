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

    /**
     * Registra una matrícula completa con su detalle en una transacción.
     * @param array $datos Cabecera y detalle de la matrícula.
     * @return bool True si fue exitoso.
     */
    public function registrarMatricula($datos) {
        $this->db->beginTransaction();

        try {
            // 1. Registrar cabecera
            $params_cabecera = [
                $datos['id_cliente'],
                $_SESSION['user_id'], // El usuario que registra
                $datos['id_forma_pago'],
                $datos['fecha_inicio_matricula'],
                $datos['fecha_fin_matricula'],
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
                    $precio_final
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
        $this->db->beginTransaction();
        try {
            // 1. Obtener el estado actual de la matrícula desde la BD
            $detalles_actuales_raw = $this->obtenerDetallesPorIdMatricula($id_matricula);
            $detalles_actuales = [];
            foreach ($detalles_actuales_raw as $detalle) {
                $detalles_actuales[$detalle['id_curso_programado']] = $detalle;
            }

            $cursos_enviados = [];
            foreach ($datos['cursos'] as $curso) {
                $cursos_enviados[$curso['id_curso_programado']] = $curso;
            }

            // 2. Determinar qué hacer con cada detalle (Añadir, Actualizar, Eliminar)
            $a_eliminar = array_diff_key($detalles_actuales, $cursos_enviados);
            $a_anadir = array_diff_key($cursos_enviados, $detalles_actuales);
            $a_actualizar = array_intersect_key($cursos_enviados, $detalles_actuales);

            // 3. Procesar eliminaciones
            foreach ($a_eliminar as $id_curso_programado => $detalle_a_eliminar) {
                // El SP se encarga de la asistencia y de recalcular totales
                $this->eliminarDetalle($detalle_a_eliminar['id_matricula_detalle'], $id_matricula);
            }

            // 4. Procesar adiciones
            foreach ($a_anadir as $id_curso_programado => $detalle_a_anadir) {
                // Aquí se deben correr las validaciones de vacantes y cruces de horario
                // (Esta lógica se puede abstraer a un método privado si se vuelve muy compleja)
                // Por simplicidad, la validación se deja al controlador por ahora, pero idealmente iría aquí.

                $precio_final = (float)$detalle_a_anadir['precio_pactado'] - (float)$detalle_a_anadir['descuento'];
                $params = [
                    $id_matricula,
                    $id_curso_programado,
                    $detalle_a_anadir['id_cliente_asistencia'],
                    $detalle_a_anadir['precio_pactado'],
                    $detalle_a_anadir['descuento'],
                    $precio_final
                ];
                $this->db->callStoredProcedure('sp_matricula_registrar_detalle', $params);
                $result = $this->db->single();
                $id_nuevo_detalle = $result['id_matricula_detalle'] ?? 0;
                if ($id_nuevo_detalle > 0) {
                    $this->db->callStoredProcedure('sp_asistencia_cliente_generar_cronograma', [$id_nuevo_detalle]);
                }
            }

            // 5. Procesar actualizaciones
            foreach ($a_actualizar as $id_curso_programado => $detalle_a_actualizar) {
                $detalle_existente = $detalles_actuales[$id_curso_programado];
                // Comprobar si algo ha cambiado antes de actualizar
                if ($detalle_existente['id_cliente_asistencia'] != $detalle_a_actualizar['id_cliente_asistencia'] ||
                    (float)$detalle_existente['precio_pactado'] != (float)$detalle_a_actualizar['precio_pactado'] ||
                    (float)$detalle_existente['descuento'] != (float)$detalle_a_actualizar['descuento']) {

                    $params_detalle = [
                        $detalle_existente['id_matricula_detalle'],
                        $detalle_a_actualizar['id_cliente_asistencia'],
                        $detalle_a_actualizar['precio_pactado'],
                        $detalle_a_actualizar['descuento']
                    ];
                    $this->db->callStoredProcedure('sp_matricula_detalle_actualizar', $params_detalle);
                }
            }

            // 6. Actualizar la cabecera de la matrícula
            $params_cabecera = [
                $id_matricula,
                $datos['id_forma_pago'],
                $datos['observaciones']
            ];
            $this->db->callStoredProcedure('sp_matricula_cabecera_actualizar', $params_cabecera);

            // 7. Recalcular totales finales
            $this->db->callStoredProcedure('sp_matricula_cabecera_recalcular', [$id_matricula]);


            $this->db->commit();
            return true;
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

    /**
     * Elimina un curso (detalle) de una matrícula y recalcula los totales.
     * @param int $id_matricula_detalle
     * @param int $id_matricula
     * @return bool
     */
    public function eliminarDetalle($id_matricula_detalle, $id_matricula) {
        $this->db->beginTransaction();
        try {
            // 1. Eliminar el detalle y su asistencia
            $this->db->callStoredProcedure('sp_matricula_detalle_eliminar', [$id_matricula_detalle]);

            // 2. Recalcular los totales de la cabecera
            $this->db->callStoredProcedure('sp_matricula_cabecera_recalcular', [$id_matricula]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Error al eliminar el detalle de la matrícula: " . $e->getMessage());
        }
    }
}
