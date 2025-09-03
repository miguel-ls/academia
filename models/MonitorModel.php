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
    public function obtenerCursosDisponibles($filtros = []) {
        // Llamamos al SP que ya habíamos diseñado para este propósito.
        $params = [
            $filtros['id_profesor'] ?? null,
            $filtros['fecha_inicio'] ?? null,
            $filtros['fecha_fin'] ?? null
        ];
        $this->db->callStoredProcedure('sp_cursos_programados_buscar_disponibles', $params);
        return $this->db->resultSet();
    }
}
