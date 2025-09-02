<?php

// =================================================================
// Controlador para el Login, Logout y 2FA
// =================================================================

require_once 'models/UsuarioModel.php';
// La clase Session ya se carga desde el index.php

$usuarioModel = new UsuarioModel();

$action = $_POST['action'] ?? $_GET['view'] ?? 'login';
$feedback_message = '';

// --- Lógica del Controlador ---

switch ($action) {
    case 'login':
        // Si el método es POST, se está intentando iniciar sesión
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $usuarioModel->obtenerPorNombreUsuario($username);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Credenciales correctas, iniciar proceso 2FA

                // 1. Generar código 2FA
                $code = rand(100000, 999999);
                // 2. Definir tiempo de expiración (ej. 5 minutos)
                $expiry = date('Y-m-d H:i:s', time() + 300);

                // 3. Guardar código y expiración en la BD
                // (Asumo que el SP `sp_usuarios_guardar_codigo_2fa` existe y funciona)
                $sp_params = [$user['id_usuario'], $code, $expiry];
                $db = new Database(); // Instanciamos Database para llamar al SP
                $db->callStoredProcedure('sp_usuarios_guardar_codigo_2fa', $sp_params);

                // 4. Simular envío de correo
                // **NOTA PARA EL USUARIO:** En un entorno real, aquí iría la lógica
                // para enviar el código por email usando una librería como PHPMailer.
                // Para esta simulación, guardaremos el código en la sesión temporalmente
                // y lo mostraremos en la siguiente página para facilitar las pruebas.
                $_SESSION['2fa_user_id'] = $user['id_usuario'];
                $_SESSION['2fa_simulated_code'] = $code; // Solo para simulación

                // 5. Redirigir a la vista de verificación 2FA
                header('Location: ' . SITE_URL . '/index.php?view=login&action=verify_2fa');
                exit;

            } else {
                // Credenciales incorrectas
                $feedback_message = "Usuario o contraseña incorrectos.";
                require_once 'views/login_view.php';
            }
        } else {
            // Si no es POST, simplemente mostrar la vista de login
            require_once 'views/login_view.php';
        }
        break;

    case 'verify_2fa':
        // Si no hay un ID de usuario en la sesión de 2FA, redirigir al login
        if (!isset($_SESSION['2fa_user_id'])) {
            header('Location: ' . SITE_URL . '/index.php?view=login');
            exit;
        }

        // Si se envía el formulario de 2FA
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code_ingresado = $_POST['code_2fa'];
            $user_id = $_SESSION['2fa_user_id'];

            // (Asumo que el SP `sp_usuarios_verificar_codigo_2fa` existe y funciona)
            $db = new Database();
            $stmt = $db->callStoredProcedure('sp_usuarios_verificar_codigo_2fa', [$user_id, $code_ingresado]);
            $result = $db->single();

            if ($result) {
                // Código correcto y no expirado
                // Limpiar variables de sesión 2FA
                unset($_SESSION['2fa_user_id']);
                unset($_SESSION['2fa_simulated_code']);

                // Crear la sesión de usuario final
                $user_data = $usuarioModel->obtenerPorId($user_id);
                Session::createUserSession($user_data);

                // Redirigir al panel principal
                header('Location: ' . SITE_URL . '/index.php?view=dashboard');
                exit;
            } else {
                // Código incorrecto o expirado
                $feedback_message = "Código de verificación incorrecto o expirado.";
                require_once 'views/2fa_verify_view.php';
            }
        } else {
            // Mostrar el formulario de 2FA
            require_once 'views/2fa_verify_view.php';
        }
        break;

    case 'logout':
        Session::destroy();
        // La función destroy ya redirige a login.php, pero por si acaso.
        header('Location: ' . SITE_URL . '/index.php?view=login');
        exit;

    default:
        // Por defecto, mostrar la vista de login
        require_once 'views/login_view.php';
        break;
}
