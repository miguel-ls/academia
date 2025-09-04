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
}
