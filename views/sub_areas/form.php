<?php require_once 'views/partials/header.php'; ?>

<?php
$is_edit = isset($item_a_editar);
$page_title = $is_edit ? 'Editar Sub Área' : 'Nueva Sub Área';
$action_url = $is_edit ? 'index.php?view=sub_areas&action=update' : 'index.php?view=sub_areas&action=create';
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
    <form action="<?php echo $action_url; ?>" method="POST" id="sub-area-form">

        <?php if ($is_edit): ?>
            <input type="hidden" name="id_sub_area" value="<?php echo $item_a_editar['id_sub_area']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="id_area">Área a la que pertenece:</label>
                <select id="id_area" name="id_area" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($areas as $area): ?>
                        <option value="<?php echo $area['id_area']; ?>"
                            <?php echo (isset($item_a_editar) && $item_a_editar['id_area'] == $area['id_area']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($area['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción (Ej: Salón 101):</label>
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
                <input type="number" id="capacidad_maxima" name="capacidad_maxima" value="<?php echo htmlspecialchars($item_a_editar['capacidad_maxima'] ?? '1'); ?>" required min="1">
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?view=sub_areas" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Actualizar Sub Área' : 'Crear Sub Área'; ?></button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/modal_error.php'; // Incluir el modal reutilizable ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('sub-area-form');
    const isEditMode = <?php echo json_encode($is_edit); ?>;

    // Solo ejecutar el script de validación AJAX en modo de edición y si el modal global existe
    if (isEditMode && window.AppModal) {
        let isSubmitting = false;

        form.addEventListener('submit', function(event) {
            if (isSubmitting) return;
            event.preventDefault();

            const idSubAreaInput = form.querySelector('input[name="id_sub_area"]');
            const capacidadInput = form.querySelector('input[name="capacidad_maxima"]');
            const data = new FormData();
            data.append('id_sub_area', idSubAreaInput.value);
            data.append('capacidad', capacidadInput.value);

            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Validando...';

            fetch('index.php?view=sub_areas&action=validar_capacidad', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    isSubmitting = true;
                    form.submit();
                } else {
                    // Usar el modal global para mostrar el error
                    window.AppModal.show(result.message, 'Error de Validación de Capacidad');
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;
                }
            })
            .catch(error => {
                console.error('Error en la llamada AJAX:', error);
                window.AppModal.show('Ocurrió un error al contactar al servidor. Por favor, intente de nuevo.');
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            });
        });
    }
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
