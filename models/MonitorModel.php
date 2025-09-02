<?php

// =================================================================
// Modelo Monitor: Obtiene datos para la vista de monitor de cursos.
// =================================================================

class MonitorModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todos los cursos programados que tienen vacantes disponibles.
     * @return array La lista de cursos disponibles.
     */
    public function obtenerCursosDisponibles() {
        // Llamamos al SP que ya habíamos diseñado para este propósito.
        // Los parámetros son opcionales (NULL), así que los pasamos como tal
        // para obtener todos los cursos disponibles sin filtrar.
        $params = [
            null, // p_profesor_id
            null, // p_fecha_inicio
            null  // p_fecha_fin
        ];
        $this->db->callStoredProcedure('sp_cursos_programados_buscar_disponibles', $params);
        return $this->db->resultSet();
    }
}
