<?php

class ListaPreciosModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_lista_precios_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_lista_precios_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function crear($datos) {
        $params = [
            $datos['id_curso'],
            $datos['id_tipo_precio'],
            $datos['precio'],
            $datos['vigencia_inicio'],
            $datos['vigencia_fin']
        ];
        $this->db->callStoredProcedure('sp_lista_precios_crear', $params);
        return $this->db->rowCount() > 0;
    }

    public function actualizar($datos) {
        $params = [
            $datos['id'],
            $datos['id_curso'],
            $datos['id_tipo_precio'],
            $datos['precio'],
            $datos['vigencia_inicio'],
            $datos['vigencia_fin']
        ];
        $this->db->callStoredProcedure('sp_lista_precios_actualizar', $params);
        return $this->db->rowCount() > 0;
    }

    public function eliminar($id) {
        $this->db->callStoredProcedure('sp_lista_precios_eliminar', [$id]);
        return $this->db->rowCount() > 0;
    }

    // Métodos para los dropdowns
    public function obtenerCursos() {
        $this->db->callStoredProcedure('sp_cursos_listar_simple');
        return $this->db->resultSet();
    }

    public function obtenerTiposDePrecio() {
        $this->db->callStoredProcedure('sp_tipos_precio_listar');
        return $this->db->resultSet();
    }
}
