<?php

class TiposHorarioModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_tipos_horario_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_tipos_horario_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function buscar($term) {
        $this->db->callStoredProcedure('sp_tipos_horario_buscar', [$term]);
        return $this->db->resultSet();
    }

    public function crear($datos) {
        try {
            $params = [
                $datos['descripcion'],
                $datos['dias_semana']
            ];
            $this->db->callStoredProcedure('sp_tipos_horario_crear', $params);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actualizar($datos) {
        try {
            $params = [
                $datos['id_tipo_horario'],
                $datos['descripcion'],
                $datos['dias_semana']
            ];
            $this->db->callStoredProcedure('sp_tipos_horario_actualizar', $params);

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
            $this->db->callStoredProcedure('sp_tipos_horario_eliminar', [$id]);
            return ['success' => true];
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                 return ['success' => false, 'error' => 'No se puede eliminar el tipo de horario porque tiene programaciones asociadas.'];
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verificarDependencias($id) {
        $this->db->callStoredProcedure('sp_tipos_horario_verificar_dependencias', [$id]);
        $resultado = $this->db->single();
        return $resultado['count'] ?? 0;
    }
}
