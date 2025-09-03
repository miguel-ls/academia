<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Mantenimiento de Formas de Pago</h1>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="<?php echo strpos($feedback_message, 'Error') === false ? 'info-message' : 'error-message'; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="form-container" style="max-width: 600px;">
    <h2><?php echo isset($item_a_editar) ? 'Editar Forma de Pago' : 'Nueva Forma de Pago'; ?></h2>
    <form action="index.php?view=formas_pago" method="POST">

        <?php if (isset($item_a_editar)): ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo $item_a_editar['id_forma_pago']; ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="create">
        <?php endif; ?>

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($item_a_editar['nombre'] ?? ''); ?>" required>
        </div>

        <div class="form-actions">
            <?php if (isset($item_a_editar)): ?>
                <a href="index.php?view=formas_pago" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary"><?php echo isset($item_a_editar) ? 'Actualizar' : 'Crear'; ?></button>
        </div>
    </form>
</div>

<h2>Lista de Formas de Pago</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item['id_forma_pago']; ?></td>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                <td>
                    <a href="index.php?view=formas_pago&action=edit&id=<?php echo $item['id_forma_pago']; ?>" class="btn btn-warning">Editar</a>
                    <form action="index.php?view=formas_pago" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $item['id_forma_pago']; ?>">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
