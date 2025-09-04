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
