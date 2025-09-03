<?php

class AreasModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_areas_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_areas_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function crear($id_tipo_area, $nombre) {
        $this->db->callStoredProcedure('sp_areas_crear', [$id_tipo_area, $nombre]);
        return $this->db->rowCount() > 0;
    }

    public function actualizar($id, $id_tipo_area, $nombre) {
        $this->db->callStoredProcedure('sp_areas_actualizar', [$id, $id_tipo_area, $nombre]);
        return $this->db->rowCount() > 0;
    }

    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_areas_eliminar', [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function obtenerTiposDeArea() {
        // Reutilizamos el SP que ya creamos para el CRUD de Tipos de Área
        $this->db->callStoredProcedure('sp_tipos_area_listar');
        return $this->db->resultSet();
    }
}
