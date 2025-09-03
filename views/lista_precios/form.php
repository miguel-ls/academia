<?php require_once 'views/partials/header.php'; ?>

<?php
$is_edit = isset($item_a_editar);
$page_title = $is_edit ? 'Editar Precio de Lista' : 'Nuevo Precio de Lista';
$action_url = $is_edit ? 'index.php?view=lista_precios&action=update' : 'index.php?view=lista_precios&action=create';
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
            <input type="hidden" name="id_lista_precio" value="<?php echo $item_a_editar['id_lista_precio']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="id_curso">Curso:</label>
                <select id="id_curso" name="id_curso" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?php echo $curso['id_curso']; ?>"
                            <?php echo (isset($item_a_editar) && $item_a_editar['id_curso'] == $curso['id_curso']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($curso['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_tipo_precio">Tipo de Precio:</label>
                <select id="id_tipo_precio" name="id_tipo_precio" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($tipos_precio as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo_precio']; ?>"
                            <?php echo (isset($item_a_editar) && $item_a_editar['id_tipo_precio'] == $tipo['id_tipo_precio']) ? 'selected' : ''; ?>>
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
                <input type="date" id="vigencia_inicio" name="vigencia_inicio" value="<?php echo htmlspecialchars($item_a_editar['vigencia_inicio'] ?? date('Y-m-d')); ?>" required>
            </div>
            <div class="form-group">
                <label for="vigencia_fin">Fin de Vigencia:</label>
                <input type="date" id="vigencia_fin" name="vigencia_fin" value="<?php echo htmlspecialchars($item_a_editar['vigencia_fin'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?view=lista_precios" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Actualizar Precio' : 'Crear Precio'; ?></button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/footer.php'; ?>
