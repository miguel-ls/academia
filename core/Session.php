<?php

// =================================================================
// Clase Session: Helpers para gestionar la sesión del usuario.
// =================================================================

class Session {

    /**
     * Verifica si hay una sesión de usuario activa.
     * Redirige a la página de login si no hay sesión.
     */
    public static function check() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            // Si no hay sesión, destruir cualquier dato de sesión y redirigir
            session_destroy();
            header('Location: index.php?view=login');
            exit();
        }
    }

    /**
     * Crea la sesión para un usuario después de un login exitoso.
     * @param object|array $user - Los datos del usuario.
     */
    public static function createUserSession($user) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre_usuario'];
        $_SESSION['user_role'] = $user['id_rol']; // Podríamos guardar el nombre del rol también
        $_SESSION['user_fullname'] = $user['nombre_completo'];
    }

    /**
     * Destruye la sesión del usuario (logout).
     */
    public static function destroy() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = []; // Vaciar el array de sesión

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        header('Location: index.php?view=login');
        exit();
    }

    /**
     * Verifica si el usuario actual tiene el rol de Administrador.
     * @return bool
     */
    public static function isAdmin() {
        // Asumimos que el rol de Administrador tiene id = 1
        return (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1);
    }
}
