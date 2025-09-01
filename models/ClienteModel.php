<?php

// =================================================================
// Modelo Cliente: Interactúa con la tabla de clientes en la BD.
// =================================================================

class ClienteModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Obtiene la lista de todos los clientes.
     */
    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_clientes_listar');
        return $this->db->resultSet();
    }

    /**
     * Obtiene un cliente por su ID.
     * @param int $id El ID del cliente.
     */
    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_clientes_obtener_por_id', [$id]);
        return $this->db->single();
    }

    /**
     * Crea un nuevo cliente.
     * @param array $datos Los datos del cliente.
     * @return int El ID del cliente creado.
     */
    public function crear($datos) {
        $params = [
            $datos['id_tipo_documento'],
            $datos['numero_documento'],
            $datos['nombres'],
            $datos['apellidos'],
            $datos['email'],
            $datos['telefono'],
            $datos['codigo_erp']
        ];
        $this->db->callStoredProcedure('sp_clientes_crear', $params);
        $result = $this->db->single();
        return $result['id_cliente'] ?? 0;
    }

    /**
     * Actualiza un cliente existente.
     * @param array $datos Los datos del cliente a actualizar.
     * @return bool True si fue exitoso, false si no.
     */
    public function actualizar($datos) {
        $params = [
            $datos['id_cliente'],
            $datos['id_tipo_documento'],
            $datos['numero_documento'],
            $datos['nombres'],
            $datos['apellidos'],
            $datos['email'],
            $datos['telefono'],
            $datos['codigo_erp']
        ];
        $this->db->callStoredProcedure('sp_clientes_actualizar', $params);
        return $this->db->rowCount() > 0;
    }

    /**
     * Busca clientes por un término de búsqueda.
     * @param string $termino El término a buscar.
     * @return array La lista de clientes que coinciden.
     */
    public function buscar($termino) {
        $this->db->callStoredProcedure('sp_clientes_buscar', [$termino]);
        return $this->db->resultSet();
    }

    // NOTA: No se implementa un método `eliminar` ya que generalmente los clientes
    // no se eliminan de forma permanente por razones de historial. Se podrían
    // desactivar si se añadiera un campo 'activo' en la tabla.
}
