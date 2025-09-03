<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Mantenimiento de Lista de Precios</h1>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="<?php echo strpos($feedback_message, 'Error') === false ? 'info-message' : 'error-message'; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <h2><?php echo isset($item_a_editar) ? 'Editar Precio' : 'Nuevo Precio'; ?></h2>
    <form action="index.php?view=lista_precios" method="POST">

        <?php if (isset($item_a_editar)): ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo $item_a_editar['id_lista_precio']; ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="create">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="id_curso">Curso:</label>
                <select id="id_curso" name="id_curso" required>
                    <option value="">-- Seleccione un curso --</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?php echo $curso['id_curso']; ?>" <?php echo (isset($item_a_editar) && $item_a_editar['id_curso'] == $curso['id_curso']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($curso['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_tipo_precio">Tipo de Precio:</label>
                <select id="id_tipo_precio" name="id_tipo_precio" required>
                    <option value="">-- Seleccione un tipo de precio --</option>
                    <?php foreach ($tipos_precio as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo_precio']; ?>" <?php echo (isset($item_a_editar) && $item_a_editar['id_tipo_precio'] == $tipo['id_tipo_precio']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="precio">Precio (S/):</label>
                <input type="number" step="0.01" id="precio" name="precio" value="<?php echo htmlspecialchars($item_a_editar['precio'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="vigencia_inicio">Inicio de Vigencia:</label>
                <input type="date" id="vigencia_inicio" name="vigencia_inicio" value="<?php echo htmlspecialchars($item_a_editar['vigencia_inicio'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="vigencia_fin">Fin de Vigencia:</label>
                <input type="date" id="vigencia_fin" name="vigencia_fin" value="<?php echo htmlspecialchars($item_a_editar['vigencia_fin'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-actions">
            <?php if (isset($item_a_editar)): ?>
                <a href="index.php?view=lista_precios" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary"><?php echo isset($item_a_editar) ? 'Actualizar' : 'Crear'; ?></button>
        </div>
    </form>
</div>

<h2>Lista de Precios Actual</h2>
<table class="table">
    <thead>
        <tr>
            <th>Curso</th>
            <th>Tipo de Precio</th>
            <th>Precio</th>
            <th>Vigencia Inicio</th>
            <th>Vigencia Fin</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['curso_nombre']); ?></td>
                <td><?php echo htmlspecialchars($item['tipo_precio_nombre']); ?></td>
                <td>S/ <?php echo number_format($item['precio'], 2); ?></td>
                <td><?php echo date('d/m/Y', strtotime($item['vigencia_inicio'])); ?></td>
                <td><?php echo date('d/m/Y', strtotime($item['vigencia_fin'])); ?></td>
                <td>
                    <a href="index.php?view=lista_precios&action=edit&id=<?php echo $item['id_lista_precio']; ?>" class="btn btn-warning">Editar</a>
                    <form action="index.php?view=lista_precios" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $item['id_lista_precio']; ?>">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
