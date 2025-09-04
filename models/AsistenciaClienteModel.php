<?php

// =================================================================
// Modelo AsistenciaCliente: Gestiona los datos de asistencia de los clientes.
// =================================================================

class AsistenciaClienteModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function listarMatriculas($filtros = []) {
        $params = [
            $filtros['id_cliente'] ?? null,
            $filtros['id_curso'] ?? null,
            $filtros['fecha_inicio'] ?? null,
            $filtros['fecha_fin'] ?? null
        ];
        $this->db->callStoredProcedure('sp_asistencia_cliente_listar_cursos', $params);
        return $this->db->resultSet();
    }

    public function obtenerDetalleMatricula($id_matricula_detalle) {
        $this->db->callStoredProcedure('sp_asistencia_cliente_obtener_detalle_curso', [$id_matricula_detalle]);
        return $this->db->single();
    }

    public function obtenerClases($id_matricula_detalle, $limit, $offset) {
        $this->db->callStoredProcedure('sp_asistencia_cliente_obtener_clases', [$id_matricula_detalle, $limit, $offset]);
        return $this->db->resultSet();
    }

    public function contarClases($id_matricula_detalle) {
        $this->db->callStoredProcedure('sp_asistencia_cliente_contar_clases', [$id_matricula_detalle]);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    public function actualizarAsistencia($id_asistencia, $estado, $observaciones) {
        // Note the different ENUM values for client attendance
        $this->db->callStoredProcedure('sp_asistencia_cliente_actualizar_asistencia', [$id_asistencia, $estado, $observaciones]);
        return $this->db->rowCount() > 0;
    }
}
