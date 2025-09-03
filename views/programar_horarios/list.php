<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Programación de Cursos</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=programar_horarios&action=new" class="btn btn-primary">Nueva Programación</a>
    </div>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Curso</th>
            <th>Profesor</th>
            <th>Ubicación</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($programaciones)): ?>
            <tr>
                <td colspan="8">No hay cursos programados.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($programaciones as $prog): ?>
                <tr>
                    <td><?php echo $prog['id_curso_programado']; ?></td>
                    <td><?php echo htmlspecialchars($prog['curso_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($prog['profesor_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($prog['ubicacion']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($prog['fecha_inicio'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($prog['fecha_fin'])); ?></td>
                    <td>
                        <span class="badge status-<?php echo strtolower($prog['estado']); ?>">
                            <?php echo htmlspecialchars($prog['estado']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="index.php?view=programar_horarios&action=edit&id=<?php echo $prog['id_curso_programado']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=programar_horarios&action=delete&id=<?php echo $prog['id_curso_programado']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta programación? Esta acción no se puede deshacer.');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
