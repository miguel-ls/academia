<?php

class TiposDocumentoModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_tipos_documento_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_tipos_documento_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function crear($datos) {
        $params = [
            $datos['descripcion'],
            $datos['longitud'],
            $datos['codigo_sunat']
        ];
        $this->db->callStoredProcedure('sp_tipos_documento_crear', $params);
        return $this->db->rowCount() > 0;
    }

    public function actualizar($datos) {
        $params = [
            $datos['id'],
            $datos['descripcion'],
            $datos['longitud'],
            $datos['codigo_sunat']
        ];
        $this->db->callStoredProcedure('sp_tipos_documento_actualizar', $params);
        return $this->db->rowCount() > 0;
    }

    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_tipos_documento_eliminar', [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
