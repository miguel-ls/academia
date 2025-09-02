<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Mantenimiento de Tipos de Horario</h1>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="<?php echo strpos($feedback_message, 'Error') === false ? 'info-message' : 'error-message'; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <h2><?php echo isset($item_a_editar) ? 'Editar Tipo de Horario' : 'Nuevo Tipo de Horario'; ?></h2>
    <form action="index.php?view=tipos_horario" method="POST">

        <?php if (isset($item_a_editar)): ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo $item_a_editar['id_tipo_horario']; ?>">
        <?php endif; ?>
        <input type="hidden" name="action" value="<?php echo isset($item_a_editar) ? 'update' : 'create'; ?>">

        <div class="form-group">
            <label for="descripcion">Descripción (Ej: Lunes, Miércoles y Viernes):</label>
            <input type="text" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($item_a_editar['descripcion'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Días de la Semana:</label>
            <?php
                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                $dias_seleccionados = isset($item_a_editar) ? explode(',', $item_a_editar['dias_semana']) : [];
            ?>
            <div class="form-row">
            <?php foreach ($dias as $i => $dia): ?>
                <div style="flex: 1 1 120px; margin-right: 15px;">
                    <input type="checkbox" name="dias_semana[]" id="dia_<?php echo $i + 1; ?>" value="<?php echo $i + 1; ?>" <?php echo in_array($i + 1, $dias_seleccionados) ? 'checked' : ''; ?>>
                    <label for="dia_<?php echo $i + 1; ?>" style="font-weight: normal; display: inline;"><?php echo $dia; ?></label>
                </div>
            <?php endforeach; ?>
            </div>
        </div>

        <div class="form-actions">
            <?php if (isset($item_a_editar)): ?>
                <a href="index.php?view=tipos_horario" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary"><?php echo isset($item_a_editar) ? 'Actualizar' : 'Crear'; ?></button>
        </div>
    </form>
</div>

<h2>Lista de Tipos de Horario</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th>Días</th>
            <th>Códigos</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $dias_map = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        ?>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item['id_tipo_horario']; ?></td>
                <td><?php echo htmlspecialchars($item['descripcion']); ?></td>
                <td>
                    <?php
                    $dias_numeros = explode(',', $item['dias_semana']);
                    $dias_texto = array_map(function($num) use ($dias_map) {
                        return $dias_map[(int)$num] ?? '';
                    }, $dias_numeros);
                    echo htmlspecialchars(implode(', ', array_filter($dias_texto)));
                    ?>
                </td>
                <td><?php echo htmlspecialchars($item['dias_semana']); ?></td>
                <td>
                    <a href="index.php?view=tipos_horario&action=edit&id=<?php echo $item['id_tipo_horario']; ?>" class="btn btn-warning">Editar</a>
                    <form action="index.php?view=tipos_horario" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $item['id_tipo_horario']; ?>">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
