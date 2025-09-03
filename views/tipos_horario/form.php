<?php require_once 'views/partials/header.php'; ?>

<?php
$is_edit = isset($item_a_editar);
$page_title = $is_edit ? 'Editar Tipo de Horario' : 'Nuevo Tipo de Horario';
$action_url = $is_edit ? 'index.php?view=tipos_horario&action=update' : 'index.php?view=tipos_horario&action=create';
?>

<div class="page-header">
    <h1><?php echo $page_title; ?></h1>
</div>

<?php if (!empty($error_message)): ?>
    <div class="info-message error-message">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form action="<?php echo $action_url; ?>" method="POST">

        <?php if ($is_edit): ?>
            <input type="hidden" name="id_tipo_horario" value="<?php echo $item_a_editar['id_tipo_horario']; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="descripcion">Descripción (Ej: Lunes, Miércoles y Viernes):</label>
            <input type="text" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($item_a_editar['descripcion'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Días de la Semana:</label>
            <?php
                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                $dias_seleccionados = isset($item_a_editar['dias_semana']) && !empty($item_a_editar['dias_semana']) ? explode(',', $item_a_editar['dias_semana']) : [];
            ?>
            <div class="form-row" style="align-items: center;">
            <?php foreach ($dias as $i => $dia): ?>
                <div style="flex: 0 1 150px; margin-right: 15px; margin-bottom: 10px;">
                    <input type="checkbox" name="dias_semana[]" id="dia_<?php echo $i + 1; ?>" value="<?php echo $i + 1; ?>" <?php echo in_array($i + 1, $dias_seleccionados) ? 'checked' : ''; ?>>
                    <label for="dia_<?php echo $i + 1; ?>" style="font-weight: normal; display: inline; padding-left: 5px;"><?php echo $dia; ?></label>
                </div>
            <?php endforeach; ?>
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?view=tipos_horario" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Actualizar Tipo' : 'Crear Tipo'; ?></button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/footer.php'; ?>
