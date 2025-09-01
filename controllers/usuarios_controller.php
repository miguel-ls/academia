<?php

// =================================================================
// Controlador para el Mantenimiento de Usuarios
// =================================================================

// Se asume que `index.php` ya ha cargado `config.php` y `core/Session.php`
require_once 'models/UsuarioModel.php';

// --- Verificación de Seguridad ---
// Solo los administradores pueden acceder a esta sección.
// La clase Session ya debería estar disponible.
if (!Session::isAdmin()) {
    // Si no es admin, redirigir al dashboard o mostrar un error.
    echo "<h1>Acceso Denegado</h1><p>No tienes permiso para acceder a esta página.</p>";
    // Opcional: header('Location: index.php?view=dashboard');
    exit();
}
// ---------------------------------


$usuarioModel = new UsuarioModel();

// --- Lógica para manejar acciones (Crear, Actualizar, Eliminar) ---
$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = (int)($_POST['id_usuario'] ?? $_GET['id_usuario'] ?? 0);
$feedback_message = ''; // Para mensajes de éxito o error.

try {
    switch ($action) {
        case 'create':
            // Lógica para crear un nuevo usuario desde un formulario POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $datos = [
                    'id_rol' => $_POST['id_rol'],
                    'nombre_usuario' => $_POST['nombre_usuario'],
                    'password_hash' => $password_hash,
                    'email' => $_POST['email'],
                    'nombre_completo' => $_POST['nombre_completo']
                ];
                $usuarioModel->crear($datos);
                $feedback_message = "Usuario creado exitosamente.";
            }
            break;

        case 'update':
            // Lógica para actualizar un usuario desde un formulario POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                $datos = [
                    'id_usuario' => $id,
                    'id_rol' => $_POST['id_rol'],
                    'nombre_usuario' => $_POST['nombre_usuario'],
                    'email' => $_POST['email'],
                    'nombre_completo' => $_POST['nombre_completo'],
                    'activo' => $_POST['activo'] ?? 0
                ];
                $usuarioModel->actualizar($datos);
                $feedback_message = "Usuario actualizado exitosamente.";
            }
            break;

        case 'delete':
            // Lógica para eliminar (lógicamente) un usuario
            if ($id > 0) {
                $usuarioModel->eliminar($id);
                $feedback_message = "Usuario desactivado exitosamente.";
            }
            break;
    }
} catch (Exception $e) {
    // En un caso real, loguearíamos el error.
    $feedback_message = "Error: " . $e->getMessage();
}


// --- Obtención de datos para la vista ---

// Obtener la lista de todos los usuarios para mostrarla en la tabla
$usuarios = $usuarioModel->obtenerTodos();

// Si la acción es 'edit', obtener los datos del usuario específico para el formulario
$usuario_a_editar = null;
if ($action === 'edit' && $id > 0) {
    $usuario_a_editar = $usuarioModel->obtenerPorId($id);
}


// --- Cargar la Vista ---
// Finalmente, incluimos el archivo de la vista que mostrará los datos.
require_once 'views/usuarios_view.php';
