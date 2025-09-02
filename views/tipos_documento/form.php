<?php require_once 'views/partials/header.php'; ?>

<?php
$is_edit = isset($item_a_editar);
$page_title = $is_edit ? 'Editar Tipo de Documento' : 'Nuevo Tipo de Documento';
$action_url = $is_edit ? 'index.php?view=tipos_documento&action=update' : 'index.php?view=tipos_documento&action=create';
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
            <input type="hidden" name="id_tipo_documento" value="<?php echo $item_a_editar['id_tipo_documento']; ?>">
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
            <a href="index.php?view=tipos_documento" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Actualizar Tipo' : 'Crear Tipo'; ?></button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/footer.php'; ?>
