<?php require_once 'views/partials/header.php'; ?>

<?php
$is_edit = isset($item_a_editar);
$page_title = $is_edit ? 'Editar Área' : 'Nueva Área';
$action_url = $is_edit ? 'index.php?view=areas&action=update' : 'index.php?view=areas&action=create';
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
            <input type="hidden" name="id_area" value="<?php echo $item_a_editar['id_area']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="nombre">Nombre del Área:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($item_a_editar['nombre'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="id_tipo_area">Tipo de Área:</label>
                <select id="id_tipo_area" name="id_tipo_area" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($tipos_area as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo_area']; ?>"
                            <?php echo (isset($item_a_editar) && $item_a_editar['id_tipo_area'] == $tipo['id_tipo_area']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?view=areas" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Actualizar Área' : 'Crear Área'; ?></button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/footer.php'; ?>
