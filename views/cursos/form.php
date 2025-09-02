<?php require_once 'views/partials/header.php'; ?>

<?php
$is_edit = isset($curso_a_editar);
$page_title = $is_edit ? 'Editar Curso' : 'Nuevo Curso';
$action_url = $is_edit ? 'index.php?view=cursos&action=update' : 'index.php?view=cursos&action=create';
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
            <input type="hidden" name="id_curso" value="<?php echo $curso_a_editar['id_curso']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="nombre">Nombre del Curso:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($curso_a_editar['nombre'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="id_tipo_curso">Tipo de Curso:</label>
                <select id="id_tipo_curso" name="id_tipo_curso" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($tipos_curso as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo_curso']; ?>"
                            <?php echo (isset($curso_a_editar) && $curso_a_editar['id_tipo_curso'] == $tipo['id_tipo_curso']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($curso_a_editar['descripcion'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="codigo_erp">Código ERP:</label>
            <input type="text" id="codigo_erp" name="codigo_erp" value="<?php echo htmlspecialchars($curso_a_editar['codigo_erp'] ?? ''); ?>">
        </div>

        <div class="form-actions">
            <a href="index.php?view=cursos" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Actualizar Curso' : 'Crear Curso'; ?></button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/footer.php'; ?>
