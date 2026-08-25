<?php

class CobroModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos($filtros = []) {
        $id_cliente = (int)($filtros['id_cliente'] ?? 0);
        $fecha_cobro = $filtros['fecha_cobro'] ?? '';
        $id_matricula = (int)($filtros['id_matricula'] ?? 0);
        $numero_operacion = $filtros['numero_operacion'] ?? '';

        $this->db->callStoredProcedure('sp_cobros_listar', [$id_cliente, $fecha_cobro, $id_matricula, $numero_operacion]);
        return $this->db->resultSet();
    }

    public function obtenerMatriculasPendientes($term = '') {
        $this->db->callStoredProcedure('sp_cobros_matriculas_pendientes', [$term]);
        return $this->db->resultSet();
    }

    public function buscarMatriculasPendientes($term = '') {
        return $this->obtenerMatriculasPendientes($term);
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_cobros_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function crear($datos) {
        try {
            $this->db->beginTransaction();
            $params = [
                $datos['id_matricula'],
                $datos['id_forma_pago'],
                $datos['fecha_cobro'],
                $datos['numero_operacion'],
                $datos['importe'],
                $datos['observaciones'],
                $_SESSION['user_id'] ?? 0,
            ];
            $this->db->callStoredProcedure('sp_cobros_crear', $params);
            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function actualizar($datos) {
        try {
            $this->db->beginTransaction();
            $params = [
                $datos['id_cobro'],
                $datos['id_forma_pago'],
                $datos['fecha_cobro'],
                $datos['numero_operacion'],
                $datos['importe'],
                $datos['observaciones'],
            ];
            $this->db->callStoredProcedure('sp_cobros_actualizar', $params);
            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_cobros_eliminar', [$id]);
            return ['success' => true];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function obtenerSaldoMatricula($id_matricula, $exclude_id = null) {
        $this->db->callStoredProcedure('sp_cobros_saldo_matricula', [$id_matricula, $exclude_id ?? 0]);
        $result = $this->db->single();
        return (float)($result['saldo_pendiente'] ?? 0.00);
    }
}
