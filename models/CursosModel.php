<?php

class CursosModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_cursos_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_cursos_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function crear($datos) {
        $params = [
            $datos['id_tipo_curso'],
            $datos['nombre'],
            $datos['descripcion'],
            $datos['codigo_erp']
        ];
        $this->db->callStoredProcedure('sp_cursos_crear', $params);
        return $this->db->rowCount() > 0;
    }

    public function actualizar($datos) {
        $params = [
            $datos['id_curso'],
            $datos['id_tipo_curso'],
            $datos['nombre'],
            $datos['descripcion'],
            $datos['codigo_erp']
        ];
        $this->db->callStoredProcedure('sp_cursos_actualizar', $params);
        return $this->db->rowCount() > 0;
    }

    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_cursos_eliminar', [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Método para obtener los tipos de curso para el dropdown del formulario
    public function obtenerTiposDeCurso() {
        $this->db->callStoredProcedure('sp_tipos_curso_listar');
        return $this->db->resultSet();
    }
}
