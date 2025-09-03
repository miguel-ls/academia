<?php

// =================================================================
// Modelo AsistenciaProfesor: Gestiona los datos de asistencia.
// =================================================================

class AsistenciaProfesorModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function listarCursosProgramados() {
        $this->db->callStoredProcedure('sp_asistencia_profesor_listar_cursos');
        return $this->db->resultSet();
    }

    public function obtenerDetalleCurso($id_curso_programado) {
        $this->db->callStoredProcedure('sp_asistencia_profesor_obtener_detalle_curso', [$id_curso_programado]);
        return $this->db->single();
    }

    public function obtenerClases($id_curso_programado) {
        $this->db->callStoredProcedure('sp_asistencia_profesor_obtener_clases', [$id_curso_programado]);
        return $this->db->resultSet();
    }

    public function actualizarAsistencia($id_asistencia, $estado, $observaciones) {
        $this->db->callStoredProcedure('sp_asistencia_profesor_actualizar_asistencia', [$id_asistencia, $estado, $observaciones]);
        return $this->db->rowCount() > 0;
    }
}
