<?php require_once 'views/partials/header.php'; ?>

<?php
$is_edit = isset($item_a_editar);
$page_title = $is_edit ? 'Editar Forma de Pago' : 'Nueva Forma de Pago';
$action_url = $is_edit ? 'index.php?view=formas_pago&action=update' : 'index.php?view=formas_pago&action=create';
?>

<div class="page-header">
    <h1><?php echo $page_title; ?></h1>
</div>

<?php if (!empty($error_message)): ?>
    <div class="info-message error-message">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<div class="form-container" style="max-width: 600px;">
    <form action="<?php echo $action_url; ?>" method="POST">

        <?php if ($is_edit): ?>
            <input type="hidden" name="id_forma_pago" value="<?php echo $item_a_editar['id_forma_pago']; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($item_a_editar['nombre'] ?? ''); ?>" required>
        </div>

        <div class="form-actions">
            <a href="index.php?view=formas_pago" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Actualizar Forma' : 'Crear Forma'; ?></button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/footer.php'; ?>
