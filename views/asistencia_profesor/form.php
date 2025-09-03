<?php
require_once 'views/partials/header.php';
$dias_es = [
    'Monday'    => 'Lunes',
    'Tuesday'   => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday'  => 'Jueves',
    'Friday'    => 'Viernes',
    'Saturday'  => 'Sábado',
    'Sunday'    => 'Domingo'
];
?>

<div class="page-header">
    <h1>Marcar Asistencia de Profesor</h1>
</div>

<!-- Course Details Section -->
<div class="card">
    <h2>Detalles del Curso Programado</h2>
    <p><strong>Curso:</strong> <?php echo htmlspecialchars($detalle_curso['curso_nombre']); ?></p>
    <p><strong>Profesor:</strong> <?php echo htmlspecialchars($detalle_curso['profesor_nombre']); ?></p>
    <p><strong>Periodo:</strong> <?php echo date('d/m/Y', strtotime($detalle_curso['fecha_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($detalle_curso['fecha_fin'])); ?></p>
    <p><strong>Horario:</strong> <?php echo htmlspecialchars($detalle_curso['tipo_horario_nombre']); ?> (<?php echo date('h:i A', strtotime($detalle_curso['hora_inicio'])); ?> - <?php echo date('h:i A', strtotime($detalle_curso['hora_fin'])); ?>)</p>
    <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($detalle_curso['ubicacion']); ?></p>
</div>

<!-- Attendance Form -->
<form action="index.php?view=asistencia_profesores&action=guardar" method="POST">
    <input type="hidden" name="id_curso_programado" value="<?php echo $id_curso_programado; ?>">

    <table class="table">
        <thead>
            <tr>
                <th>Fecha de Clase</th>
                <th>Estado</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clases)): ?>
                <tr>
                    <td colspan="3">No hay clases generadas para este curso.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($clases as $clase): ?>
                    <tr>
                        <td>
                            <?php
                                $dia_ingles = date('l', strtotime($clase['fecha_clase']));
                                $dia_espanol = $dias_es[$dia_ingles] ?? $dia_ingles;
                                echo date('d/m/Y', strtotime($clase['fecha_clase'])) . ' (' . $dia_espanol . ')';
                            ?>
                        </td>
                        <td>
                            <input type="hidden" name="asistencia[<?php echo $clase['id_asistencia_profesor']; ?>][id]" value="<?php echo $clase['id_asistencia_profesor']; ?>">
                            <select name="asistencia[<?php echo $clase['id_asistencia_profesor']; ?>][estado]" required>
                                <option value="Programado" <?php echo ($clase['estado'] == 'Programado') ? 'selected' : ''; ?>>Programado</option>
                                <option value="Asistió" <?php echo ($clase['estado'] == 'Asistió') ? 'selected' : ''; ?>>Asistió</option>
                                <option value="Faltó" <?php echo ($clase['estado'] == 'Faltó') ? 'selected' : ''; ?>>Faltó</option>
                                <option value="Reprogramado" <?php echo ($clase['estado'] == 'Reprogramado') ? 'selected' : ''; ?>>Reprogramado</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="asistencia[<?php echo $clase['id_asistencia_profesor']; ?>][observaciones]" value="<?php echo htmlspecialchars($clase['observaciones']); ?>" style="width: 100%;">
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginación -->
    <?php if ($total_paginas > 1): ?>
        <div class="pagination-container">
            <?php if ($pagina_actual > 1): ?>
                <a href="index.php?view=asistencia_profesores&action=marcar&id=<?php echo $id_curso_programado; ?>&page=<?php echo $pagina_actual - 1; ?>" class="btn">&laquo; Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="index.php?view=asistencia_profesores&action=marcar&id=<?php echo $id_curso_programado; ?>&page=<?php echo $i; ?>" class="btn <?php echo ($i == $pagina_actual) ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($pagina_actual < $total_paginas): ?>
                <a href="index.php?view=asistencia_profesores&action=marcar&id=<?php echo $id_curso_programado; ?>&page=<?php echo $pagina_actual + 1; ?>" class="btn">Siguiente &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="form-actions">
        <a href="index.php?view=asistencia_profesores" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Grabar Asistencia</button>
    </div>
</form>

<?php require_once 'views/partials/footer.php'; ?>
