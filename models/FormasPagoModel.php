<?php

class FormasPagoModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_formas_pago_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_formas_pago_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function buscar($term) {
        $this->db->callStoredProcedure('sp_formas_pago_buscar', [$term]);
        return $this->db->resultSet();
    }

    public function crear($datos) {
        try {
            $params = [$datos['nombre']];
            $this->db->callStoredProcedure('sp_formas_pago_crear', $params);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actualizar($datos) {
        try {
            $params = [$datos['id_forma_pago'], $datos['nombre']];
            $this->db->callStoredProcedure('sp_formas_pago_actualizar', $params);

            if ($this->db->rowCount() > 0) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'No se realizaron cambios.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_formas_pago_eliminar', [$id]);
            return ['success' => true];
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                 return ['success' => false, 'error' => 'No se puede eliminar la forma de pago porque está en uso en matrículas.'];
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verificarDependencias($id) {
        $this->db->callStoredProcedure('sp_formas_pago_verificar_dependencias', [$id]);
        $resultado = $this->db->single();
        return $resultado['count'] ?? 0;
    }
}
