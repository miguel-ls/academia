<?php

// =================================================================
// Controlador para el Login, Logout y 2FA
// =================================================================

require_once 'models/UsuarioModel.php';

$usuarioModel = new UsuarioModel();
$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $usuarioModel->obtenerPorNombreUsuario($username);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Contraseña correcta, iniciar proceso 2FA
                $code = rand(100000, 999999);
                $expiry = date('Y-m-d H:i:s', time() + 300);

                $db = Database::getInstance();
                $db->callStoredProcedure('sp_usuarios_guardar_codigo_2fa', [$user['id_usuario'], $code, $expiry]);

                // Guardar datos necesarios para el siguiente paso en la sesión
                $_SESSION['2fa_user_id'] = $user['id_usuario'];
                $_SESSION['2fa_simulated_code'] = $code; // Para simulación

                // Redirigir a la página de verificación
                header('Location: index.php?view=login&action=verify_2fa');
                exit;
            } else {
                // Credenciales incorrectas, guardar mensaje en sesión y redirigir
                $_SESSION['error_message'] = 'Usuario o contraseña incorrectos.';
                header('Location: index.php?view=login');
                exit;
            }
        } else {
            // Si es GET, simplemente mostrar la vista de login
            require_once 'views/login/login_view.php';
        }
        break;

    case 'verify_2fa':
        // Si no hay un ID de usuario en la sesión de 2FA, redirigir al login
        if (!isset($_SESSION['2fa_user_id'])) {
            header('Location: index.php?view=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code_2fa'] ?? '';
            $userId = $_SESSION['2fa_user_id'];

            $db = Database::getInstance();
            $stmt = $db->callStoredProcedure('sp_usuarios_verificar_codigo_2fa', [$userId, $code]);
            $result = $db->single();

            if ($result) {
                // Éxito: limpiar código 2FA y establecer sesión final
                unset($_SESSION['2fa_user_id']);
                unset($_SESSION['2fa_simulated_code']);

                $user_data = $usuarioModel->obtenerPorId($userId);
                Session::createUserSession($user_data);

                header('Location: index.php?view=dashboard');
                exit;
            } else {
                // Código incorrecto, guardar error y redirigir
                $_SESSION['error_message'] = 'El código de verificación es incorrecto o ha expirado.';
                header('Location: index.php?view=login&action=verify_2fa');
                exit;
            }
        } else {
            // Si es GET, mostrar la vista de verificación
            require_once 'views/login/2fa_verify_view.php';
        }
        break;

    default:
        // Por defecto, mostrar la vista de login
        require_once 'views/login/login_view.php';
        break;
}
