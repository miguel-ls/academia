<?php

class TiposAreaModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_tipos_area_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_tipos_area_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function crear($nombre) {
        $this->db->callStoredProcedure('sp_tipos_area_crear', [$nombre]);
        return $this->db->rowCount() > 0;
    }

    public function actualizar($id, $nombre) {
        $this->db->callStoredProcedure('sp_tipos_area_actualizar', [$id, $nombre]);
        return $this->db->rowCount() > 0;
    }

    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_tipos_area_eliminar', [$id]);
            return true;
        } catch (Exception $e) {
            // Capturar error de clave foránea
            return false;
        }
    }
}
