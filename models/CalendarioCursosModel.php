<?php
// =================================================================
// Modelo CalendarioCursos: Gestiona los datos para el calendario
// de cursos programados.
// =================================================================

class CalendarioCursosModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todos los cursos programados para el calendario.
     * @return array Lista de cursos programados.
     */
    public function obtenerCursosProgramados() {
        try {
            // Llamar al nuevo SP que devuelve datos extendidos.
            // No se necesitan filtros para el calendario general.
            $this->db->callStoredProcedure('sp_calendario_programacion_listar', [null, null, null, null]);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error en CalendarioCursosModel::obtenerCursosProgramados: " . $e->getMessage());
            return [];
        }
    }
}
