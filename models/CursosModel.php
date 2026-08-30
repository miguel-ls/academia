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

    public function buscar($term) {
        $this->db->callStoredProcedure('sp_cursos_buscar', [$term]);
        return $this->db->resultSet();
    }

    public function crear($datos) {
        try {
            $params = [
                $datos['id_tipo_curso'],
                $datos['categoria_erp'],
                $datos['grupo_erp'],
                $datos['clase_erp'],
                $datos['familia_erp'],
                $datos['nombre'],
                $datos['descripcion'],
                $datos['codigo_erp']
            ];
            $this->db->callStoredProcedure('sp_cursos_crear', $params);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actualizar($datos) {
        try {
            $params = [
                $datos['id_curso'],
                $datos['id_tipo_curso'],
                $datos['categoria_erp'],
                $datos['grupo_erp'],
                $datos['clase_erp'],
                $datos['familia_erp'],
                $datos['nombre'],
                $datos['descripcion'],
                $datos['codigo_erp']
            ];
            $this->db->callStoredProcedure('sp_cursos_actualizar', $params);

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
            $this->db->callStoredProcedure('sp_cursos_eliminar', [$id]);
            return ['success' => true];
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                 return ['success' => false, 'error' => 'No se puede eliminar el curso porque está en uso.'];
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verificarDependencias($id) {
        $this->db->callStoredProcedure('sp_curso_verificar_dependencias', [$id]);
        $resultado = $this->db->single();
        return $resultado['count'] ?? 0;
    }

    public function obtenerTiposDeCurso() {
        $this->db->callStoredProcedure('sp_tipos_curso_listar');
        return $this->db->resultSet();
    }

    public function obtenerCategorias() {
        $this->db->callStoredProcedure('sp_categorias_listar');
        return $this->db->resultSet();
    }

    public function obtenerGrupos($categoria) {
        $this->db->callStoredProcedure('sp_grupos_listar_por_categoria', [$categoria]);
        return $this->db->resultSet();
    }

    public function obtenerClases($categoria, $grupo) {
        $this->db->callStoredProcedure('sp_clases_listar_por_categoria_grupo', [$categoria, $grupo]);
        return $this->db->resultSet();
    }

    public function obtenerFamilias($categoria, $grupo, $clase) {
        $this->db->callStoredProcedure('sp_familias_listar_por_categoria_grupo_clase', [$categoria, $grupo, $clase]);
        return $this->db->resultSet();
    }

    public function clasificacionExiste($categoria, $grupo, $clase, $familia) {
        foreach ($this->obtenerFamilias($categoria, $grupo, $clase) as $item) {
            if ($item['codigo'] === $familia) {
                return true;
            }
        }
        return false;
    }
}
