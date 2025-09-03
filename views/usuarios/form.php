<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1><?php echo isset($usuario_a_editar) ? 'Editar Usuario' : 'Crear Nuevo Usuario'; ?></h1>
</div>

<?php if (!empty($error_message)): ?>
    <div class="error-message">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form action="index.php?view=usuarios&action=<?php echo isset($usuario_a_editar) ? 'update' : 'create'; ?>" method="POST">

        <?php if (isset($usuario_a_editar)): ?>
            <input type="hidden" name="id_usuario" value="<?php echo $usuario_a_editar['id_usuario']; ?>">
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
            <a href="index.php?view=usuarios" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo isset($usuario_a_editar) ? 'Actualizar Usuario' : 'Crear Usuario'; ?></button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/footer.php'; ?>
