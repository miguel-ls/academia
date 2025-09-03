<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1><?php echo isset($programacion_a_editar) ? 'Editar Programación' : 'Programar Nuevo Curso'; ?></h1>
    <?php if (!isset($programacion_a_editar)): ?>
        <p style="margin: 0;">Complete el formulario para añadir un nuevo curso al cronograma.</p>
    <?php endif; ?>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === false ? '' : 'error-message'; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form action="index.php?view=programar_horarios&action=<?php echo isset($programacion_a_editar) ? 'update' : 'create'; ?>" method="POST">

        <?php if (isset($programacion_a_editar)): ?>
            <input type="hidden" name="id_curso_programado" value="<?php echo $programacion_a_editar['id_curso_programado']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="id_curso">Curso:</label>
                <select id="id_curso" name="id_curso" required>
                    <option value="">-- Seleccione un curso --</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?php echo $curso['id_curso']; ?>" <?php echo (isset($programacion_a_editar) && $programacion_a_editar['id_curso'] == $curso['id_curso']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($curso['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_profesor">Profesor:</label>
                <select id="id_profesor" name="id_profesor" required>
                    <option value="">-- Seleccione un profesor --</option>
                    <?php foreach ($profesores as $profesor): ?>
                        <option value="<?php echo $profesor['id_profesor']; ?>" <?php echo (isset($programacion_a_editar) && $programacion_a_editar['id_profesor'] == $profesor['id_profesor']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($profesor['nombre_completo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="id_sub_area">Ubicación (Área - Subárea):</label>
                <select id="id_sub_area" name="id_sub_area" required>
                    <option value="">-- Seleccione una ubicación --</option>
                    <?php foreach ($sub_areas as $sub_area): ?>
                        <option value="<?php echo $sub_area['id_sub_area']; ?>" <?php echo (isset($programacion_a_editar) && $programacion_a_editar['id_sub_area'] == $sub_area['id_sub_area']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sub_area['nombre_completo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_tipo_horario">Tipo de Horario (Días):</label>
                <select id="id_tipo_horario" name="id_tipo_horario" required>
                    <option value="">-- Seleccione un tipo de horario --</option>
                    <?php foreach ($tipos_horario as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo_horario']; ?>" <?php echo (isset($programacion_a_editar) && $programacion_a_editar['id_tipo_horario'] == $tipo['id_tipo_horario']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['descripcion']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="fecha_inicio">Vigencia - Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($programacion_a_editar['fecha_inicio'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="fecha_fin">Vigencia - Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($programacion_a_editar['fecha_fin'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="hora_inicio">Hora de Inicio:</label>
                <input type="time" id="hora_inicio" name="hora_inicio" value="<?php echo htmlspecialchars($programacion_a_editar['hora_inicio'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="hora_fin">Hora de Fin:</label>
                <input type="time" id="hora_fin" name="hora_fin" value="<?php echo htmlspecialchars($programacion_a_editar['hora_fin'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="vacantes">Vacantes Disponibles:</label>
            <input type="number" id="vacantes" name="vacantes" min="1" value="<?php echo htmlspecialchars($programacion_a_editar['vacantes'] ?? ''); ?>" required>
        </div>

        <div class="form-actions">
             <a href="index.php?view=programar_horarios" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo isset($programacion_a_editar) ? 'Actualizar Programación' : 'Programar Curso'; ?></button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/footer.php'; ?>
