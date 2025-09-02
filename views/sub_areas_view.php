<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Mantenimiento de Sub Áreas</h1>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="<?php echo strpos($feedback_message, 'Error') === false ? 'info-message' : 'error-message'; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <h2><?php echo isset($item_a_editar) ? 'Editar Sub Área' : 'Nueva Sub Área'; ?></h2>
    <form action="index.php?view=sub_areas" method="POST">

        <?php if (isset($item_a_editar)): ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo $item_a_editar['id_sub_area']; ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="create">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="id_area">Área a la que pertenece:</label>
                <select id="id_area" name="id_area" required>
                    <option value="">-- Seleccione un área --</option>
                    <?php foreach ($areas as $area): ?>
                        <option value="<?php echo $area['id_area']; ?>" <?php echo (isset($item_a_editar) && $item_a_editar['id_area'] == $area['id_area']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($area['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción (Ej: Salón 101, Piscina A):</label>
                <input type="text" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($item_a_editar['descripcion'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="numero_sub_area">Número / Código de Sub Área:</label>
                <input type="text" id="numero_sub_area" name="numero_sub_area" value="<?php echo htmlspecialchars($item_a_editar['numero_sub_area'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="capacidad_maxima">Capacidad Máxima:</label>
                <input type="number" id="capacidad_maxima" name="capacidad_maxima" value="<?php echo htmlspecialchars($item_a_editar['capacidad_maxima'] ?? ''); ?>" required min="1">
            </div>
        </div>

        <div class="form-actions">
            <?php if (isset($item_a_editar)): ?>
                <a href="index.php?view=sub_areas" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary"><?php echo isset($item_a_editar) ? 'Actualizar' : 'Crear'; ?></button>
        </div>
    </form>
</div>

<h2>Lista de Sub Áreas</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Área Principal</th>
            <th>Descripción</th>
            <th>Número</th>
            <th>Capacidad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item['id_sub_area']; ?></td>
                <td><?php echo htmlspecialchars($item['area_nombre']); ?></td>
                <td><?php echo htmlspecialchars($item['descripcion']); ?></td>
                <td><?php echo htmlspecialchars($item['numero_sub_area']); ?></td>
                <td><?php echo htmlspecialchars($item['capacidad_maxima']); ?></td>
                <td>
                    <a href="index.php?view=sub_areas&action=edit&id=<?php echo $item['id_sub_area']; ?>" class="btn btn-warning">Editar</a>
                    <form action="index.php?view=sub_areas" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $item['id_sub_area']; ?>">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
