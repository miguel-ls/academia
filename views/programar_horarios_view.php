<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Programar Nuevo Curso</h1>
    <p style="margin: 0;">Complete el formulario para añadir un nuevo curso al cronograma.</p>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === false ? '' : 'error-message'; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form action="index.php?view=programar_horarios" method="POST">
        <input type="hidden" name="action" value="programar">

        <div class="form-row">
            <div class="form-group">
                <label for="id_curso">Curso:</label>
                <select id="id_curso" name="id_curso" required>
                    <option value="">-- Seleccione un curso --</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?php echo $curso['id_curso']; ?>"><?php echo htmlspecialchars($curso['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_profesor">Profesor:</label>
                <select id="id_profesor" name="id_profesor" required>
                    <option value="">-- Seleccione un profesor --</option>
                    <?php foreach ($profesores as $profesor): ?>
                        <option value="<?php echo $profesor['id_profesor']; ?>"><?php echo htmlspecialchars($profesor['nombre_completo']); ?></option>
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
                        <option value="<?php echo $sub_area['id_sub_area']; ?>"><?php echo htmlspecialchars($sub_area['nombre_completo']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_tipo_horario">Tipo de Horario (Días):</label>
                <select id="id_tipo_horario" name="id_tipo_horario" required>
                    <option value="">-- Seleccione un tipo de horario --</option>
                    <?php foreach ($tipos_horario as $tipo): ?>
                        <option value="<?php echo $tipo['id_tipo_horario']; ?>"><?php echo htmlspecialchars($tipo['descripcion']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="fecha_inicio">Vigencia - Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>

            <div class="form-group">
                <label for="fecha_fin">Vigencia - Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="hora_inicio">Hora de Inicio:</label>
                <input type="time" id="hora_inicio" name="hora_inicio" required>
            </div>

            <div class="form-group">
                <label for="hora_fin">Hora de Fin:</label>
                <input type="time" id="hora_fin" name="hora_fin" required>
            </div>
        </div>

        <div class="form-group">
            <label for="vacantes">Vacantes Disponibles:</label>
            <input type="number" id="vacantes" name="vacantes" min="1" required>
        </div>

        <div class="form-actions">
             <a href="index.php?view=dashboard" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Programar Curso</button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/footer.php'; ?>
