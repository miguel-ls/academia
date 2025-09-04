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

    public function buscar($term) {
        $this->db->callStoredProcedure('sp_sub_areas_buscar', [$term]);
        return $this->db->resultSet();
    }

    public function crear($datos) {
        try {
            $params = [
                $datos['id_area'],
                $datos['descripcion'],
                $datos['numero_sub_area'],
                $datos['capacidad_maxima']
            ];
            $this->db->callStoredProcedure('sp_sub_areas_crear', $params);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actualizar($datos) {
        try {
            $params = [
                $datos['id_sub_area'],
                $datos['id_area'],
                $datos['descripcion'],
                $datos['numero_sub_area'],
                $datos['capacidad_maxima']
            ];
            $this->db->callStoredProcedure('sp_sub_areas_actualizar', $params);

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
            $this->db->callStoredProcedure('sp_sub_areas_eliminar', [$id]);
            return ['success' => true];
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                 return ['success' => false, 'error' => 'No se puede eliminar la sub-área porque tiene horarios programados.'];
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verificarDependencias($id) {
        $this->db->callStoredProcedure('sp_sub_areas_verificar_dependencias', [$id]);
        $resultado = $this->db->single();
        return $resultado['count'] ?? 0;
    }

    public function validarCapacidad($id_sub_area, $capacidad_maxima) {
        $this->db->callStoredProcedure('sp_sub_areas_validar_capacidad', [$id_sub_area, $capacidad_maxima]);
        return $this->db->single();
    }

    public function obtenerAreas() {
        $this->db->callStoredProcedure('sp_areas_listar');
        return $this->db->resultSet();
    }
}
