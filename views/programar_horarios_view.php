<?php
// =================================================================
// Vista para la Programación de Horarios
// =================================================================
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programar Horarios - <?php echo SITE_NAME; ?></title>
    <!-- Reutilizamos el mismo estilo simple -->
    <style>
        body { font-family: sans-serif; }
        .form-container { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; max-width: 600px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .feedback { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .feedback.success { background-color: #d4edda; color: #155724; }
        .feedback.error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <!-- Incluir un encabezado/menú común -->
    <?php // include 'partials/header.php'; ?>
    <a href="index.php?view=dashboard">Volver al Panel</a>
    <hr>

    <h1>Programar Nuevo Curso</h1>
    <p>Complete el formulario para añadir un nuevo curso al cronograma.</p>

    <?php if (!empty($feedback_message)): ?>
        <div class="feedback <?php echo strpos($feedback_message, 'Error') === false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form action="index.php?view=programar_horarios" method="POST">
            <input type="hidden" name="action" value="programar">

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

            <div class="form-group">
                <label for="fecha_inicio">Vigencia - Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>

            <div class="form-group">
                <label for="fecha_fin">Vigencia - Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>
            </div>

            <div class="form-group">
                <label for="hora_inicio">Hora de Inicio:</label>
                <input type="time" id="hora_inicio" name="hora_inicio" required>
            </div>

            <div class="form-group">
                <label for="hora_fin">Hora de Fin:</label>
                <input type="time" id="hora_fin" name="hora_fin" required>
            </div>

            <div class="form-group">
                <label for="vacantes">Vacantes Disponibles:</label>
                <input type="number" id="vacantes" name="vacantes" min="1" required>
            </div>

            <button type="submit">Programar Curso</button>
        </form>
    </div>

</body>
</html>
