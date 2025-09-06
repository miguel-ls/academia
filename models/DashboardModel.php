<?php

// =================================================================
// Modelo Dashboard: Obtiene datos para los gráficos del panel.
// =================================================================

class DashboardModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene los datos de ventas por curso para un mes y año.
     * @param int $anio
     * @param int $mes
     * @return array
     */
    public function getVentasPorCurso($anio, $mes) {
        $this->db->callStoredProcedure('sp_dashboard_ventas_por_curso', [$anio, $mes]);
        return $this->db->resultSet();
    }

    /**
     * Obtiene los datos de ventas mensuales para un año.
     * @param int $anio
     * @return array
     */
    public function getVentasMensuales($anio) {
        $this->db->callStoredProcedure('sp_dashboard_ventas_mensuales', [$anio]);
        return $this->db->resultSet();
    }

    /**
     * Obtiene los datos de ventas por curso-area para un mes y año.
     * @param int $anio
     * @param int $mes
     * @return array
     */
    public function getVentasPorCursoArea($anio, $mes) {
        $this->db->callStoredProcedure('sp_dashboard_ventas_mes_por_curso_area', [$anio, $mes]);
        return $this->db->resultSet();
    }
}
