<?php
// =================================================================
// Vista para el Mantenimiento de Usuarios
// =================================================================

// Se asume que este archivo es incluido por un controlador que ya ha definido
// las variables $usuarios, $usuario_a_editar, y $feedback_message.

// Incluir un encabezado común para todas las páginas del panel
// include 'partials/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento de Usuarios - <?php echo SITE_NAME; ?></title>
    <!-- Aquí irían los estilos CSS, por ejemplo, de Bootstrap -->
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .form-container { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; }
        .feedback { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .feedback.success { background-color: #d4edda; color: #155724; }
        .feedback.error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <h1>Mantenimiento de Usuarios</h1>
    <p>Desde aquí puede gestionar los usuarios del sistema.</p>

    <?php if (!empty($feedback_message)): ?>
        <div class="feedback <?php echo strpos($feedback_message, 'Error') === false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para Crear o Editar Usuarios -->
    <div class="form-container">
        <h2><?php echo isset($usuario_a_editar) ? 'Editar Usuario' : 'Crear Nuevo Usuario'; ?></h2>
        <form action="index.php?view=usuarios" method="POST">

            <!-- Si estamos editando, incluimos el ID y la acción 'update' -->
            <?php if (isset($usuario_a_editar)): ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id_usuario" value="<?php echo $usuario_a_editar['id_usuario']; ?>">
            <?php else: ?>
                <input type="hidden" name="action" value="create">
            <?php endif; ?>

            <div>
                <label for="nombre_completo">Nombre Completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo htmlspecialchars($usuario_a_editar['nombre_completo'] ?? ''); ?>" required>
            </div>
            <br>
            <div>
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario_a_editar['nombre_usuario'] ?? ''); ?>" required>
            </div>
            <br>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario_a_editar['email'] ?? ''); ?>" required>
            </div>
            <br>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" <?php echo isset($usuario_a_editar) ? '' : 'required'; ?>>
                <?php if (isset($usuario_a_editar)): ?>
                    <small>Dejar en blanco para no cambiar la contraseña.</small>
                <?php endif; ?>
            </div>
            <br>
            <div>
                <label for="id_rol">Rol:</label>
                <select id="id_rol" name="id_rol" required>
                    <!-- Aquí se deberían cargar los roles desde la BD, por ahora los hardcodeamos -->
                    <option value="1" <?php echo (isset($usuario_a_editar) && $usuario_a_editar['id_rol'] == 1) ? 'selected' : ''; ?>>Administrador</option>
                    <option value="2" <?php echo (isset($usuario_a_editar) && $usuario_a_editar['id_rol'] == 2) ? 'selected' : ''; ?>>Usuario</option>
                </select>
            </div>
            <br>
            <?php if (isset($usuario_a_editar)): ?>
            <div>
                <label for="activo">Estado:</label>
                <select id="activo" name="activo">
                    <option value="1" <?php echo $usuario_a_editar['activo'] == 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo $usuario_a_editar['activo'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <br>
            <?php endif; ?>

            <button type="submit"><?php echo isset($usuario_a_editar) ? 'Actualizar Usuario' : 'Crear Usuario'; ?></button>
            <?php if (isset($usuario_a_editar)): ?>
                <a href="index.php?view=usuarios">Cancelar Edición</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabla con la lista de usuarios -->
    <h2>Lista de Usuarios</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="7">No hay usuarios registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo $usuario['id_usuario']; ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre_rol']); ?></td>
                        <td><?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?></td>
                        <td>
                            <a href="index.php?view=usuarios&action=edit&id_usuario=<?php echo $usuario['id_usuario']; ?>">Editar</a>
                            <!-- El borrado se hace con un formulario para usar POST y más seguridad -->
                            <form action="index.php?view=usuarios&action=delete&id_usuario=<?php echo $usuario['id_usuario']; ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de que desea desactivar este usuario?');">
                                <button type="submit">Desactivar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>

<?php
// Incluir un pie de página común
// include 'partials/footer.php';
?>
