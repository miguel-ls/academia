<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Usuarios</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=usuarios&action=new" class="btn btn-primary">Nuevo Usuario</a>
    </div>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

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
        <?php if (empty($usuarios)): ?>
            <tr>
                <td colspan="7">No se encontraron usuarios.</td>
            </tr>
        <?php else: ?>
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
                        <a href="index.php?view=usuarios&action=edit&id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=usuarios&action=delete&id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea desactivar este usuario?');">Desactivar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
