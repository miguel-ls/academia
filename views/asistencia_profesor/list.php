<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Asistencia de Profesores</h1>
    </div>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<!-- Add search filters here in the future -->

<table class="table">
    <thead>
        <tr>
            <th>ID Prog.</th>
            <th>Curso</th>
            <th>Profesor</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($cursos_programados)): ?>
            <tr>
                <td colspan="7">No hay cursos programados para mostrar.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($cursos_programados as $curso): ?>
                <tr>
                    <td><?php echo $curso['id_curso_programado']; ?></td>
                    <td><?php echo htmlspecialchars($curso['curso_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($curso['profesor_nombre']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($curso['fecha_inicio'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($curso['fecha_fin'])); ?></td>
                    <td>
                        <span class="badge status-<?php echo strtolower($curso['estado']); ?>">
                            <?php echo htmlspecialchars($curso['estado']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="index.php?view=asistencia_profesores&action=marcar&id=<?php echo $curso['id_curso_programado']; ?>" class="btn btn-primary">Marcar Asistencia</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
