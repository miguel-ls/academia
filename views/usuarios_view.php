<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Mantenimiento de Usuarios</h1>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="info-message">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<!-- Formulario para Crear o Editar Usuarios -->
<div class="form-container">
    <h2><?php echo isset($usuario_a_editar) ? 'Editar Usuario' : 'Crear Nuevo Usuario'; ?></h2>
    <form action="index.php?view=usuarios" method="POST">

        <?php if (isset($usuario_a_editar)): ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id_usuario" value="<?php echo $usuario_a_editar['id_usuario']; ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="create">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="nombre_completo">Nombre Completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo htmlspecialchars($usuario_a_editar['nombre_completo'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario_a_editar['nombre_usuario'] ?? ''); ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario_a_editar['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" <?php echo isset($usuario_a_editar) ? '' : 'required'; ?>>
                <?php if (isset($usuario_a_editar)): ?><small>Dejar en blanco para no cambiar la contraseña.</small><?php endif; ?>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="id_rol">Rol:</label>
                <select id="id_rol" name="id_rol" required>
                    <option value="1" <?php echo (isset($usuario_a_editar) && $usuario_a_editar['id_rol'] == 1) ? 'selected' : ''; ?>>Administrador</option>
                    <option value="2" <?php echo (isset($usuario_a_editar) && $usuario_a_editar['id_rol'] == 2) ? 'selected' : ''; ?>>Usuario</option>
                </select>
            </div>
            <?php if (isset($usuario_a_editar)): ?>
            <div class="form-group">
                <label for="activo">Estado:</label>
                <select id="activo" name="activo">
                    <option value="1" <?php echo $usuario_a_editar['activo'] == 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo $usuario_a_editar['activo'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <?php if (isset($usuario_a_editar)): ?>
                <a href="index.php?view=usuarios" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary"><?php echo isset($usuario_a_editar) ? 'Actualizar Usuario' : 'Crear Usuario'; ?></button>
        </div>
    </form>
</div>

<!-- Tabla con la lista de usuarios -->
<h2>Lista de Usuarios</h2>
<table class="table">
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
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo $usuario['id_usuario']; ?></td>
                <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                <td><?php echo htmlspecialchars($usuario['nombre_usuario']); ?></td>
                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                <td><?php echo htmlspecialchars($usuario['nombre_rol']); ?></td>
                <td>
                    <?php if ($usuario['activo']): ?>
                        <span class="badge status-activo">Activo</span>
                    <?php else: ?>
                        <span class="badge status-inactivo">Inactivo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="index.php?view=usuarios&action=edit&id_usuario=<?php echo $usuario['id_usuario']; ?>" class="btn btn-warning">Editar</a>
                    <form action="index.php?view=usuarios&action=delete&id_usuario=<?php echo $usuario['id_usuario']; ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de que desea desactivar este usuario?');">
                        <button type="submit" class="btn btn-danger">Desactivar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
