<?php

// =================================================================
// Modelo Usuario: Interactúa con la tabla de usuarios en la BD.
// =================================================================

class UsuarioModel {
    private $db;

    public function __construct() {
        // La clase Database se instancia una vez y se pasa o se instancia aquí.
        // Por simplicidad, la instanciamos aquí.
        $this->db = new Database();
    }

    /**
     * Obtiene la lista de todos los usuarios.
     */
    public function obtenerTodos() {
        $this->db->callStoredProcedure('sp_usuarios_listar');
        return $this->db->resultSet();
    }

    /**
     * Obtiene un usuario por su ID.
     * @param int $id El ID del usuario.
     */
    public function obtenerPorId($id) {
        $this->db->callStoredProcedure('sp_usuarios_obtener_por_id', [$id]);
        return $this->db->single();
    }

    /**
     * Crea un nuevo usuario.
     * @param array $datos Los datos del usuario.
     * @return bool True si fue exitoso, false si no.
     */
    public function crear($datos) {
        // El password debe ser hasheado antes de llegar aquí.
        $params = [
            $datos['id_rol'],
            $datos['nombre_usuario'],
            $datos['password_hash'],
            $datos['email'],
            $datos['nombre_completo']
        ];
        $this->db->callStoredProcedure('sp_usuarios_crear', $params);
        return $this->db->rowCount() > 0;
    }

    /**
     * Actualiza un usuario existente.
     * @param array $datos Los datos del usuario a actualizar.
     * @return bool True si fue exitoso, false si no.
     */
    public function actualizar($datos) {
        $params = [
            $datos['id_usuario'],
            $datos['id_rol'],
            $datos['nombre_usuario'],
            $datos['email'],
            $datos['nombre_completo'],
            $datos['activo']
        ];
        $this->db->callStoredProcedure('sp_usuarios_actualizar', $params);
        return $this->db->rowCount() > 0;
    }

    /**
     * Cambia la contraseña de un usuario.
     * @param int $id El ID del usuario.
     * @param string $newPasswordHash El nuevo password hasheado.
     * @return bool True si fue exitoso, false si no.
     */
    public function cambiarPassword($id, $newPasswordHash) {
        $this->db->callStoredProcedure('sp_usuarios_cambiar_password', [$id, $newPasswordHash]);
        return $this->db->rowCount() > 0;
    }

    /**
     * Elimina (lógicamente) un usuario.
     * @param int $id El ID del usuario a eliminar.
     * @return bool True si fue exitoso, false si no.
     */
    public function eliminar($id) {
        $this->db->callStoredProcedure('sp_usuarios_eliminar', [$id]);
        return $this->db->rowCount() > 0;
    }

    /**
     * Obtiene un usuario por su nombre de usuario para el proceso de login.
     * @param string $username
     */
    public function obtenerPorNombreUsuario($username) {
        $this->db->callStoredProcedure('sp_usuarios_obtener_por_nombre', [$username]);
        return $this->db->single();
    }
}
