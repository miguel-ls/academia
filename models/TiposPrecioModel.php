<?php

class TiposPrecioModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_tipos_precio_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_tipos_precio_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function crear($nombre) {
        $this->db->callStoredProcedure('sp_tipos_precio_crear', [$nombre]);
        return $this->db->rowCount() > 0;
    }

    public function actualizar($id, $nombre) {
        $this->db->callStoredProcedure('sp_tipos_precio_actualizar', [$id, $nombre]);
        return $this->db->rowCount() > 0;
    }

    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_tipos_precio_eliminar', [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
