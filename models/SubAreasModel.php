<?php

class SubAreasModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_sub_areas_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_sub_areas_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function crear($datos) {
        $params = [
            $datos['id_area'],
            $datos['descripcion'],
            $datos['numero_sub_area'],
            $datos['capacidad_maxima']
        ];
        $this->db->callStoredProcedure('sp_sub_areas_crear', $params);
        return $this->db->rowCount() > 0;
    }

    public function actualizar($datos) {
        $params = [
            $datos['id_sub_area'],
            $datos['id_area'],
            $datos['descripcion'],
            $datos['numero_sub_area'],
            $datos['capacidad_maxima']
        ];
        $this->db->callStoredProcedure('sp_sub_areas_actualizar', $params);
        return $this->db->rowCount() > 0;
    }

    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_sub_areas_eliminar', [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Método para obtener las áreas para el dropdown del formulario
    public function obtenerAreas() {
        // Puedo crear un sp_areas_listar_simple o reutilizar el existente
        $this->db->callStoredProcedure('sp_areas_listar');
        return $this->db->resultSet();
    }
}
