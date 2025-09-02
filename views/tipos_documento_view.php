<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Mantenimiento de Tipos de Documento</h1>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="<?php echo strpos($feedback_message, 'Error') === false ? 'info-message' : 'error-message'; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <h2><?php echo isset($item_a_editar) ? 'Editar Tipo de Documento' : 'Nuevo Tipo de Documento'; ?></h2>
    <form action="index.php?view=tipos_documento" method="POST">

        <?php if (isset($item_a_editar)): ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo $item_a_editar['id_tipo_documento']; ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="create">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <input type="text" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($item_a_editar['descripcion'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="longitud">Longitud (opcional):</label>
                <input type="number" id="longitud" name="longitud" value="<?php echo htmlspecialchars($item_a_editar['longitud'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="codigo_sunat">Código SUNAT (opcional):</label>
                <input type="text" id="codigo_sunat" name="codigo_sunat" value="<?php echo htmlspecialchars($item_a_editar['codigo_sunat'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-actions">
            <?php if (isset($item_a_editar)): ?>
                <a href="index.php?view=tipos_documento" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary"><?php echo isset($item_a_editar) ? 'Actualizar' : 'Crear'; ?></button>
        </div>
    </form>
</div>

<h2>Lista de Tipos de Documento</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th>Longitud</th>
            <th>Código SUNAT</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item['id_tipo_documento']; ?></td>
                <td><?php echo htmlspecialchars($item['descripcion']); ?></td>
                <td><?php echo htmlspecialchars($item['longitud']); ?></td>
                <td><?php echo htmlspecialchars($item['codigo_sunat']); ?></td>
                <td>
                    <a href="index.php?view=tipos_documento&action=edit&id=<?php echo $item['id_tipo_documento']; ?>" class="btn btn-warning">Editar</a>
                    <form action="index.php?view=tipos_documento" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $item['id_tipo_documento']; ?>">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
