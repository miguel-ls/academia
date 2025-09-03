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

    public function crear($datos) {
        $params = [
            $datos['descripcion'],
            $datos['dias_semana']
        ];
        $this->db->callStoredProcedure('sp_tipos_horario_crear', $params);
        return $this->db->rowCount() > 0;
    }

    public function actualizar($datos) {
        $params = [
            $datos['id'],
            $datos['descripcion'],
            $datos['dias_semana']
        ];
        $this->db->callStoredProcedure('sp_tipos_horario_actualizar', $params);
        return $this->db->rowCount() > 0;
    }

    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_tipos_horario_eliminar', [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
