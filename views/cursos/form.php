<?php require_once 'views/partials/header.php'; ?>

<?php
$is_edit = isset($curso_a_editar['id_curso']);
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
                <label for="categoria_erp">Categoría:</label>
                <select id="categoria_erp" name="categoria_erp" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo htmlspecialchars($categoria['codigo']); ?>" <?php echo (($curso_a_editar['categoria_erp'] ?? '') === $categoria['codigo']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['codigo'] . ' - ' . $categoria['descripcion']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="grupo_erp">Grupo:</label>
                <select id="grupo_erp" name="grupo_erp" required <?php echo empty($grupos) ? 'disabled' : ''; ?>>
                    <option value="">Seleccione...</option>
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?php echo htmlspecialchars($grupo['codigo']); ?>" <?php echo (($curso_a_editar['grupo_erp'] ?? '') === $grupo['codigo']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($grupo['codigo'] . ' - ' . $grupo['descripcion']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="clase_erp">Clase:</label>
                <select id="clase_erp" name="clase_erp" required <?php echo empty($clases) ? 'disabled' : ''; ?>>
                    <option value="">Seleccione...</option>
                    <?php foreach ($clases as $clase): ?>
                        <option value="<?php echo htmlspecialchars($clase['codigo']); ?>" <?php echo (($curso_a_editar['clase_erp'] ?? '') === $clase['codigo']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($clase['codigo'] . ' - ' . $clase['descripcion']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="familia_erp">Familia:</label>
                <select id="familia_erp" name="familia_erp" required <?php echo empty($familias) ? 'disabled' : ''; ?>>
                    <option value="">Seleccione...</option>
                    <?php foreach ($familias as $familia): ?>
                        <option value="<?php echo htmlspecialchars($familia['codigo']); ?>" <?php echo (($curso_a_editar['familia_erp'] ?? '') === $familia['codigo']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($familia['codigo'] . ' - ' . $familia['descripcion']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categoria = document.getElementById('categoria_erp');
    const grupo = document.getElementById('grupo_erp');
    const clase = document.getElementById('clase_erp');
    const familia = document.getElementById('familia_erp');

    function resetSelect(select, message) {
        select.innerHTML = '<option value="">' + message + '</option>';
        select.disabled = true;
    }

    async function loadOptions(select, action, params, placeholder, selectedValue) {
        resetSelect(select, 'Cargando...');
        const response = await fetch('index.php?view=cursos&action=' + action + '&' + new URLSearchParams(params));
        const result = await response.json();
        if (!result.success) throw new Error(result.error || 'No se pudieron cargar los datos.');

        select.innerHTML = '<option value="">' + placeholder + '</option>';
        result.data.forEach(function (item) {
            const option = document.createElement('option');
            option.value = item.codigo;
            option.textContent = item.codigo + ' - ' + item.descripcion;
            option.selected = item.codigo === selectedValue;
            select.appendChild(option);
        });
        select.disabled = false;
    }

    async function loadGrupos(selectedValue) {
        resetSelect(clase, 'Seleccione un grupo...');
        resetSelect(familia, 'Seleccione una clase...');
        if (!categoria.value) return;
        await loadOptions(grupo, 'obtener_grupos', { categoria: categoria.value }, 'Seleccione...', selectedValue || '');
    }

    async function loadClases(selectedValue) {
        resetSelect(familia, 'Seleccione una clase...');
        if (!categoria.value || !grupo.value) return;
        await loadOptions(clase, 'obtener_clases', { categoria: categoria.value, grupo: grupo.value }, 'Seleccione...', selectedValue || '');
    }

    async function loadFamilias(selectedValue) {
        if (!categoria.value || !grupo.value || !clase.value) return;
        await loadOptions(familia, 'obtener_familias', { categoria: categoria.value, grupo: grupo.value, clase: clase.value }, 'Seleccione...', selectedValue || '');
    }

    function handleError() {
        resetSelect(grupo, 'No se pudieron cargar los grupos.');
        resetSelect(clase, 'Seleccione un grupo...');
        resetSelect(familia, 'Seleccione una clase...');
    }

    categoria.addEventListener('change', function () { loadGrupos('').catch(handleError); });
    grupo.addEventListener('change', function () { loadClases('').catch(handleError); });
    clase.addEventListener('change', function () { loadFamilias('').catch(handleError); });

    if (categoria.value && grupo.options.length === 1) {
        loadGrupos('').catch(handleError);
    }
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
