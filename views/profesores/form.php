<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1><?php echo isset($profesor_a_editar) ? 'Editar Profesor' : 'Nuevo Profesor'; ?></h1>
</div>

<?php if (!empty($error_message)): ?>
    <div class="error-message">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form action="index.php?view=profesores&action=<?php echo isset($profesor_a_editar) ? 'update' : 'create'; ?>" method="POST">

        <?php if (isset($profesor_a_editar)): ?>
            <input type="hidden" name="id_profesor" value="<?php echo $profesor_a_editar['id_profesor']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="nombres">Nombres:</label>
                <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($profesor_a_editar['nombres'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($profesor_a_editar['apellidos'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="id_tipo_documento">Tipo de Documento:</label>
                <select id="id_tipo_documento" name="id_tipo_documento" required>
                    <option value="">Seleccione...</option>
                    <?php if (!empty($tipos_documento)): ?>
                        <?php foreach ($tipos_documento as $tipo): ?>
                            <option value="<?php echo $tipo['id_tipo_documento']; ?>" <?php echo (isset($profesor_a_editar) && $profesor_a_editar['id_tipo_documento'] == $tipo['id_tipo_documento']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tipo['descripcion']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="numero_documento">Número de Documento:</label>
                <input type="text" id="numero_documento" name="numero_documento" value="<?php echo htmlspecialchars($profesor_a_editar['numero_documento'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profesor_a_editar['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($profesor_a_editar['telefono'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="especialidad">Especialidad / Tipo de Curso:</label>
                <select id="especialidad" name="especialidad" required>
                     <option value="">Seleccione...</option>
                     <?php if (!empty($tipos_curso)): ?>
                        <?php foreach ($tipos_curso as $tipo): ?>
                            <option value="<?php echo htmlspecialchars($tipo['nombre']); ?>" <?php echo (isset($profesor_a_editar) && $profesor_a_editar['especialidad'] == $tipo['nombre']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tipo['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?view=profesores" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo isset($profesor_a_editar) ? 'Actualizar Profesor' : 'Crear Profesor'; ?></button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/footer.php'; ?>
