<?php

// =================================================================
// Modelo Cliente: Interactúa con la tabla de clientes en la BD.
// =================================================================

class ClienteModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
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
     * @return array Resultado con 'success' (bool) y 'id' o 'error' (string).
     */
    public function crear($datos) {
        $params = [
            $datos['id_tipo_documento'],
            $datos['numero_documento'],
            $datos['nombres'],
            $datos['apellidos'],
            $datos['email'],
            $datos['telefono'],
            $datos['codigo_erp'],
            $datos['direccion'],
            $datos['codigo_ubigeo'],
            $datos['estado']
        ];
        try {
            $this->db->callStoredProcedure('sp_clientes_crear', $params);
            $result = $this->db->single();
            return ['success' => true, 'id' => $result['id_cliente'] ?? 0];
        } catch (PDOException $e) {
            // Log the detailed error for the developer
            error_log("PDOException in ClienteModel::crear: " . $e->getMessage());

            // Check if it's a user-defined exception (like duplicate document)
            if ($e->getCode() == '45000') {
                // Return the specific message from the stored procedure
                return ['success' => false, 'error' => $e->errorInfo[2]];
            } else {
                // Return a generic error for other database issues
                return ['success' => false, 'error' => 'Ocurrió un error en la base de datos al crear el cliente.'];
            }
        }
    }

    /**
     * Actualiza un cliente existente.
     * @param array $datos Los datos del cliente a actualizar.
     * @return array Resultado con 'success' (bool) y 'error' (string) si falla.
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
            $datos['codigo_erp'],
            $datos['direccion'],
            $datos['codigo_ubigeo'],
            $datos['estado']
        ];
        try {
            $this->db->callStoredProcedure('sp_clientes_actualizar', $params);
            return ['success' => $this->db->rowCount() > 0];
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                return ['success' => false, 'error' => $e->errorInfo[2]];
            }
            throw $e;
        }
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

    /**
     * Verifica si un cliente tiene matrículas asociadas.
     * @param int $id El ID del cliente.
     * @return int El número de matrículas.
     */
    public function verificarMatriculas($id) {
        $this->db->callStoredProcedure('sp_cliente_verificar_matriculas', [$id]);
        $result = $this->db->single();
        return (int)($result['numero_matriculas'] ?? 0);
    }

    /**
     * Elimina un cliente por su ID.
     * @param int $id El ID del cliente a eliminar.
     * @return bool True si fue exitoso, false si no.
     */
    public function eliminar($id) {
        try {
            $this->db->callStoredProcedure('sp_clientes_eliminar', [$id]);
            return $this->db->rowCount() > 0;
        } catch (PDOException $e) {
            // Podría fallar por la FK si la validación en el controlador se omite
            return false;
        }
    }

    /**
     * Verifica si un número de documento ya existe, excluyendo un ID de cliente.
     * @param string $numero_documento El número de documento a verificar.
     * @param int|null $id_cliente_excluir El ID del cliente a excluir de la búsqueda.
     * @return bool True si existe, false si no.
     */
    public function verificarDocumentoExistente($numero_documento, $id_cliente_excluir = null) {
        $this->db->callStoredProcedure('sp_cliente_verificar_documento_existente', [$numero_documento, $id_cliente_excluir]);
        $result = $this->db->single();
        return (int)($result['exists'] ?? 0) > 0;
    }
}
