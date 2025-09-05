<?php
// =================================================================
// Modelo Calendario: Gestiona los datos para el calendario de clases.
// =================================================================

class CalendarioModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todos los cursos activos de todos los clientes para el calendario.
     * @return array Lista de cursos activos.
     */
    public function obtenerCursosActivos() {
        try {
            $this->db->callStoredProcedure('sp_calendario_cursos_activos');
            return $this->db->resultSet();
        } catch (Exception $e) {
            // Manejar o registrar el error según sea necesario
            error_log("Error en CalendarioModel::obtenerCursosActivos: " . $e->getMessage());
            return [];
        }
    }
}
