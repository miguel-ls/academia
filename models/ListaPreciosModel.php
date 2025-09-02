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

    public function buscar($term) {
        $this->db->callStoredProcedure('sp_lista_precios_buscar', [$term]);
        return $this->db->resultSet();
    }

    public function crear($datos) {
        try {
            $params = [
                $datos['id_curso'],
                $datos['id_tipo_precio'],
                $datos['precio'],
                $datos['vigencia_inicio'],
                $datos['vigencia_fin']
            ];
            $this->db->callStoredProcedure('sp_lista_precios_crear', $params);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actualizar($datos) {
        try {
            $params = [
                $datos['id_lista_precio'],
                $datos['id_curso'],
                $datos['id_tipo_precio'],
                $datos['precio'],
                $datos['vigencia_inicio'],
                $datos['vigencia_fin']
            ];
            $this->db->callStoredProcedure('sp_lista_precios_actualizar', $params);

            if ($this->db->rowCount() > 0) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'No se realizaron cambios.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_lista_precios_eliminar', [$id]);
            return ['success' => true];
        } catch (Exception $e) {
            // No se esperan dependencias directas, pero se captura por si acaso
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // No hay dependencias directas que verificar para eliminar un precio de lista
    public function verificarDependencias($id) {
        return 0;
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
