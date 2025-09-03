<?php

// =================================================================
// Controlador para el Mantenimiento de Usuarios (Refactorizado)
// =================================================================

require_once 'models/UsuarioModel.php';

// --- Verificación de Seguridad ---
Session::check();
if (!Session::isAdmin()) {
    require_once 'views/partials/header.php';
    echo '<div class="page-header"><h1>Acceso Denegado</h1></div>';
    echo '<div class="card" style="padding: 20px;"><p>No tienes permiso para acceder a esta sección.</p>';
    echo '<a href="index.php?view=dashboard" class="btn">Volver al Panel</a></div>';
    require_once 'views/partials/footer.php';
    exit();
}
// ---------------------------------

$usuarioModel = new UsuarioModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']); // Limpiar mensaje

$error_message = '';
$usuarios = [];
$usuario_a_editar = null;

try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (empty($_POST['password'])) {
                     $_SESSION['feedback_message'] = "Error: La contraseña es obligatoria para crear un usuario.";
                     header('Location: index.php?view=usuarios&action=new');
                     exit();
                }

                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $datos = [
                    'id_rol' => $_POST['id_rol'],
                    'nombre_usuario' => $_POST['nombre_usuario'],
                    'password_hash' => $password_hash,
                    'email' => $_POST['email'],
                    'nombre_completo' => $_POST['nombre_completo']
                ];
                $resultado = $usuarioModel->crear($datos);
                if ($resultado) {
                    $_SESSION['feedback_message'] = "Usuario creado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el usuario.";
                }
                header('Location: index.php?view=usuarios');
                exit();
            }
            header('Location: index.php?view=usuarios&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id_usuario = (int)($_POST['id_usuario'] ?? 0);
                if ($id_usuario <= 0) {
                    $_SESSION['feedback_message'] = "Error: ID de usuario no válido.";
                    header('Location: index.php?view=usuarios');
                    exit();
                }

                $datos = [
                    'id_usuario' => $id_usuario,
                    'id_rol' => $_POST['id_rol'],
                    'nombre_usuario' => $_POST['nombre_usuario'],
                    'email' => $_POST['email'],
                    'nombre_completo' => $_POST['nombre_completo'],
                    'activo' => $_POST['activo'] ?? 0
                ];

                if (!empty($_POST['password'])) {
                    $datos['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                $resultado = $usuarioModel->actualizar($datos);

                if ($resultado) {
                    $_SESSION['feedback_message'] = "Usuario actualizado exitosamente.";
                } else {
                    $_SESSION['feedback_message'] = "Error al actualizar el usuario o no se realizaron cambios.";
                }
                header('Location: index.php?view=usuarios');
                exit();
            }
            header('Location: index.php?view=usuarios');
            exit();

        case 'delete':
            if ($id > 0) {
                if ($id === (int)$_SESSION['user_id']) {
                     $_SESSION['feedback_message'] = "Error: No puedes desactivar tu propia cuenta.";
                } else {
                    if ($usuarioModel->eliminar($id)) {
                        $_SESSION['feedback_message'] = "Usuario desactivado exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo desactivar el usuario.";
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de usuario no válido.";
            }
            header('Location: index.php?view=usuarios');
            exit();

        case 'new':
            require_once 'views/usuarios/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $usuario_a_editar = $usuarioModel->obtenerPorId($id);
                if (!$usuario_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Usuario no encontrado.";
                    header('Location: index.php?view=usuarios');
                    exit();
                }
                require_once 'views/usuarios/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de usuario no válido.";
                 header('Location: index.php?view=usuarios');
                 exit();
            }
            break;

        case 'list':
        default:
            $usuarios = $usuarioModel->obtenerTodos();
            require_once 'views/usuarios/list.php';
            break;
    }

} catch (Exception $e) {
    $_SESSION['feedback_message'] = "Error inesperado en el sistema: " . $e->getMessage();
    header('Location: index.php?view=usuarios');
    exit();
}
