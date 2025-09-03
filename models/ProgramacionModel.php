<?php

// =================================================================
// Modelo Programacion: Gestiona la programación de cursos y
// la generación de cronogramas.
// =================================================================

class ProgramacionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Crea un nuevo curso programado y su cronograma de asistencia.
     * @param array $datos Los datos del formulario de programación.
     * @return bool True si fue exitoso, false si no.
     */
    public function programarCurso($datos) {
        $params_crear = [
            $datos['id_curso'],
            $datos['id_profesor'],
            $datos['id_sub_area'],
            $datos['id_tipo_horario'],
            $datos['fecha_inicio'],
            $datos['fecha_fin'],
            $datos['hora_inicio'],
            $datos['hora_fin'],
            $datos['vacantes']
        ];

        // 1. Crear el curso programado
        $stmt = $this->db->callStoredProcedure('sp_cursos_programados_crear', $params_crear);
        $result = $this->db->single();
        $id_curso_programado = $result['id_curso_programado'] ?? 0;

        if ($id_curso_programado > 0) {
            // 2. Si se creó correctamente, generar el cronograma de asistencia del profesor
            $this->db->callStoredProcedure('sp_asistencia_profesor_generar_cronograma', [$id_curso_programado]);
            return true;
        }

        return false;
    }

    /**
     * Obtiene las listas necesarias para los dropdowns del formulario.
     * @return array Un array con las listas de cursos, profesores, etc.
     */
    public function obtenerListasParaFormulario() {
        $listas = [];

        $this->db->callStoredProcedure('sp_cursos_listar_simple');
        $listas['cursos'] = $this->db->resultSet();

        $this->db->callStoredProcedure('sp_profesores_listar_simple');
        $listas['profesores'] = $this->db->resultSet();

        $this->db->callStoredProcedure('sp_sub_areas_listar');
        $listas['sub_areas'] = $this->db->resultSet();

        $this->db->callStoredProcedure('sp_tipos_horario_listar_simple');
        $listas['tipos_horario'] = $this->db->resultSet();

        return $listas;
    }

    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_cursos_programados_listar');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_cursos_programados_obtener_por_id', [$id]);
        return $this->db->single();
    }

    public function actualizar($datos) {
        $params = [
            $datos['id_curso_programado'],
            $datos['id_curso'],
            $datos['id_profesor'],
            $datos['id_sub_area'],
            $datos['id_tipo_horario'],
            $datos['fecha_inicio'],
            $datos['fecha_fin'],
            $datos['hora_inicio'],
            $datos['hora_fin'],
            $datos['vacantes']
        ];
        $this->db->callStoredProcedure('sp_cursos_programados_actualizar', $params);
        return $this->db->rowCount() > 0;
    }

    public function eliminar($id) {
        $this->db->callStoredProcedure('sp_cursos_programados_eliminar', [$id]);
        return $this->db->rowCount() > 0;
    }

    public function getProfesorSchedulesInRange($profesorId, $startDate, $endDate, $excludeScheduleId = null) {
        $this->db->callStoredProcedure('sp_cursos_programados_por_profesor_en_rango', [$profesorId, $startDate, $endDate, $excludeScheduleId]);
        return $this->db->resultSet();
    }

    public function getDiasSemanaByTipoHorarioId($tipoHorarioId) {
        $this->db->callStoredProcedure('sp_tipos_horario_obtener_por_id', [$tipoHorarioId]);
        $result = $this->db->single();
        return $result ? $result['dias_semana'] : null;
    }
}
