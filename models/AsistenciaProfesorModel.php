<?php

// =================================================================
// Modelo AsistenciaProfesor: Gestiona los datos de asistencia.
// =================================================================

class AsistenciaProfesorModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function listarCursosProgramados($filtros = []) {
        $params = [
            $filtros['id_profesor'] ?? null,
            $filtros['id_curso'] ?? null,
            $filtros['fecha_inicio'] ?? null,
            $filtros['fecha_fin'] ?? null
        ];
        $this->db->callStoredProcedure('sp_asistencia_profesor_listar_cursos', $params);
        return $this->db->resultSet();
    }

    public function obtenerDetalleCurso($id_curso_programado) {
        $this->db->callStoredProcedure('sp_asistencia_profesor_obtener_detalle_curso', [$id_curso_programado]);
        return $this->db->single();
    }

    public function obtenerClases($id_curso_programado, $limit, $offset) {
        $this->db->callStoredProcedure('sp_asistencia_profesor_obtener_clases', [$id_curso_programado, $limit, $offset]);
        return $this->db->resultSet();
    }

    public function contarClases($id_curso_programado) {
        $this->db->callStoredProcedure('sp_asistencia_profesor_contar_clases', [$id_curso_programado]);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    public function actualizarAsistencia($id_asistencia, $estado, $observaciones) {
        $this->db->callStoredProcedure('sp_asistencia_profesor_actualizar_asistencia', [$id_asistencia, $estado, $observaciones]);
        return $this->db->rowCount() > 0;
    }
}
