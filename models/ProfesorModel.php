<?php

// =================================================================
// Modelo Profesor: Interactúa con la tabla de profesores en la BD.
// =================================================================

class ProfesorModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_profesores_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_profesores_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function crear($datos) {
        $params = [
            $datos['id_tipo_documento'],
            $datos['numero_documento'],
            $datos['nombres'],
            $datos['apellidos'],
            $datos['email'],
            $datos['telefono'],
            $datos['especialidad']
        ];
        $this->db->callStoredProcedure('sp_profesores_crear', $params);
        return $this->db->rowCount() > 0;
    }

    public function actualizar($datos) {
        $params = [
            $datos['id_profesor'],
            $datos['id_tipo_documento'],
            $datos['numero_documento'],
            $datos['nombres'],
            $datos['apellidos'],
            $datos['email'],
            $datos['telefono'],
            $datos['especialidad']
        ];
        $this->db->callStoredProcedure('sp_profesores_actualizar', $params);
        return $this->db->rowCount() > 0;
    }

    public function eliminar($id) {
        $this->db->callStoredProcedure('sp_profesores_eliminar', [$id]);
        return $this->db->rowCount() > 0;
    }

    public function buscar($termino) {
        $this->db->callStoredProcedure('sp_profesores_buscar', [$termino]);
        return $this->db->resultSet();
    }
}
