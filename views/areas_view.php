<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Mantenimiento de Áreas</h1>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="<?php echo strpos($feedback_message, 'Error') === false ? 'info-message' : 'error-message'; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <h2><?php echo isset($item_a_editar) ? 'Editar Área' : 'Nueva Área'; ?></h2>
    <form action="index.php?view=areas" method="POST">

        <?php if (isset($item_a_editar)): ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo $item_a_editar['id_area']; ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="create">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="nombre">Nombre del Área:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($item_a_editar['nombre'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="id_tipo_area">Tipo de Área:</label>
                <select id="id_tipo_area" name="id_tipo_area" required>
                    <option value="">-- Seleccione un tipo --</option>
                    <?php foreach ($tipos_area as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo_area']; ?>" <?php echo (isset($item_a_editar) && $item_a_editar['id_tipo_area'] == $tipo['id_tipo_area']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <?php if (isset($item_a_editar)): ?>
                <a href="index.php?view=areas" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary"><?php echo isset($item_a_editar) ? 'Actualizar' : 'Crear'; ?></button>
        </div>
    </form>
</div>

<h2>Lista de Áreas</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo de Área</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item['id_area']; ?></td>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                <td><?php echo htmlspecialchars($item['tipo_area']); ?></td>
                <td>
                    <a href="index.php?view=areas&action=edit&id=<?php echo $item['id_area']; ?>" class="btn btn-warning">Editar</a>
                    <form action="index.php?view=areas" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $item['id_area']; ?>">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
