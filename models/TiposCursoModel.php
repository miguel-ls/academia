<?php

class TiposCursoModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_tipos_curso_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_tipos_curso_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function crear($nombre) {
        $this->db->callStoredProcedure('sp_tipos_curso_crear', [$nombre]);
        return $this->db->rowCount() > 0;
    }

    public function actualizar($id, $nombre) {
        $this->db->callStoredProcedure('sp_tipos_curso_actualizar', [$id, $nombre]);
        return $this->db->rowCount() > 0;
    }

    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_tipos_curso_eliminar', [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
