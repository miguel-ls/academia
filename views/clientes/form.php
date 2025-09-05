<?php require_once 'views/partials/header.php'; ?>

<?php
$is_edit = isset($cliente_a_editar);
$page_title = $is_edit ? 'Editar Cliente' : 'Nuevo Cliente';
$action_url = $is_edit ? 'index.php?view=clientes&action=update' : 'index.php?view=clientes&action=create';
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
    <form id="cliente-form" action="<?php echo $action_url; ?>" method="POST">

        <?php if ($is_edit): ?>
            <input type="hidden" id="id_cliente" name="id_cliente" value="<?php echo $cliente_a_editar['id_cliente']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="id_tipo_documento">Tipo de Documento:</label>
                <select id="id_tipo_documento" name="id_tipo_documento" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($tipos_documento as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo_documento']; ?>"
                            <?php echo (isset($cliente_a_editar) && $cliente_a_editar['id_tipo_documento'] == $tipo['id_tipo_documento']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['descripcion']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="numero_documento">Número de Documento:</label>
                <input type="text" id="numero_documento" name="numero_documento" value="<?php echo htmlspecialchars($cliente_a_editar['numero_documento'] ?? ''); ?>" required>
                <div id="documento-error" class="validation-error" style="display: none; color: red; font-size: 0.9em;"></div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="nombres" id="label_nombres">Nombres:</label>
                <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($cliente_a_editar['nombres'] ?? ''); ?>" required>
            </div>
            <div class="form-group" id="group_apellidos">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($cliente_a_editar['apellidos'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-row">
             <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cliente_a_editar['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente_a_editar['telefono'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($cliente_a_editar['direccion'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="codigo_ubigeo">Código de Ubigeo:</label>
                <input type="text" id="codigo_ubigeo" name="codigo_ubigeo" value="<?php echo htmlspecialchars($cliente_a_editar['codigo_ubigeo'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="codigo_erp">Código ERP:</label>
            <input type="text" id="codigo_erp" name="codigo_erp" value="<?php echo htmlspecialchars($cliente_a_editar['codigo_erp'] ?? ''); ?>">
        </div>

        <div class="form-actions">
            <a href="index.php?view=clientes" class="btn btn-secondary">Cancelar</a>
            <button type="submit" id="submit-btn" class="btn btn-primary"><?php echo $is_edit ? 'Actualizar Cliente' : 'Crear Cliente'; ?></button>
        </div>
    </form>
</div>

<!-- Incluir el nuevo archivo JS -->
<script src="public/assets/js/cliente_form.js"></script>

<?php require_once 'views/partials/footer.php'; ?>
