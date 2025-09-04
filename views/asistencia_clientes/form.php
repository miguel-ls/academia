<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Marcar Asistencia de Cliente</h1>
</div>

<!-- Enrollment Details Section -->
<div class="card">
    <h2>Detalles de la Matrícula</h2>
    <p><strong>Curso:</strong> <?php echo htmlspecialchars($detalle_matricula['curso_nombre']); ?></p>
    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($detalle_matricula['cliente_nombre']); ?></p>
    <p><strong>Profesor:</strong> <?php echo htmlspecialchars($detalle_matricula['profesor_nombre']); ?></p>
    <p><strong>Periodo:</strong> <?php echo date('d/m/Y', strtotime($detalle_matricula['fecha_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($detalle_matricula['fecha_fin'])); ?></p>
    <p><strong>Horario:</strong> <?php echo htmlspecialchars($detalle_matricula['tipo_horario_nombre']); ?> (<?php echo date('h:i A', strtotime($detalle_matricula['hora_inicio'])); ?> - <?php echo date('h:i A', strtotime($detalle_matricula['hora_fin'])); ?>)</p>
    <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($detalle_matricula['ubicacion']); ?></p>
</div>

<div class="page-header-right">
    <button type="button" id="btn-modificar-asistencia" class="btn btn-warning">Modificar Asistencias</button>
</div>
<!-- Attendance Form -->
<form action="index.php?view=asistencia_clientes&action=guardar" method="POST">
    <input type="hidden" name="id_matricula_detalle" value="<?php echo $id; ?>">

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
                    <td colspan="3">No hay clases generadas para esta matrícula.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($clases as $clase): ?>
                    <tr>
                        <td>
                            <?php echo date('d/m/Y', strtotime($clase['fecha_clase'])) . ' (' . htmlspecialchars($clase['dia_semana_es']) . ')'; ?>
                        </td>
                        <td class="cell-estado">
                            <span class="badge status-<?php echo strtolower(htmlspecialchars($clase['estado'])); ?> view-mode">
                                <?php echo htmlspecialchars($clase['estado']); ?>
                            </span>
                            <select name="asistencias[<?php echo $clase['id_asistencia_cliente']; ?>][estado]" required class="edit-mode" style="display:none;">
                                <option value="Programado" <?php echo ($clase['estado'] == 'Programado') ? 'selected' : ''; ?>>Programado</option>
                                <option value="Asistió" <?php echo ($clase['estado'] == 'Asistió') ? 'selected' : ''; ?>>Asistió</option>
                                <option value="Faltó" <?php echo ($clase['estado'] == 'Faltó') ? 'selected' : ''; ?>>Faltó</option>
                                <option value="Justificado" <?php echo ($clase['estado'] == 'Justificado') ? 'selected' : ''; ?>>Justificado</option>
                                <option value="Postergado" <?php echo ($clase['estado'] == 'Postergado') ? 'selected' : ''; ?>>Postergado</option>
                                <option value="Cancelado" <?php echo ($clase['estado'] == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                        </td>
                        <td class="cell-observaciones">
                            <span class="view-mode"><?php echo htmlspecialchars($clase['observaciones']); ?></span>
                            <input type="text" name="asistencias[<?php echo $clase['id_asistencia_cliente']; ?>][observaciones]" value="<?php echo htmlspecialchars($clase['observaciones']); ?>" style="width: 100%; display:none;" class="edit-mode">
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginación -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination-container">
            <?php if ($page > 1): ?>
                <a href="index.php?view=asistencia_clientes&action=marcar&id=<?php echo $id; ?>&page=<?php echo $page - 1; ?>" class="btn btn-primary">&laquo; Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="index.php?view=asistencia_clientes&action=marcar&id=<?php echo $id; ?>&page=<?php echo $i; ?>" class="btn <?php echo ($i == $page) ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="index.php?view=asistencia_clientes&action=marcar&id=<?php echo $id; ?>&page=<?php echo $page + 1; ?>" class="btn btn-primary">Siguiente &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="form-actions edit-mode" style="display:none;">
        <a href="index.php?view=asistencia_clientes" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Grabar Asistencia</button>
    </div>
</form>

<style>
.badge {
    padding: 5px 10px;
    border-radius: 12px;
    color: #fff;
    font-weight: bold;
    font-size: 0.9em;
    text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
}
.status-programado { background-color: #17a2b8; } /* Info */
.status-asistió { background-color: #28a745; } /* Success */
.status-faltó { background-color: #dc3545; } /* Danger */
.status-justificado { background-color: #ffc107; color: #212529; } /* Warning */
.status-postergado { background-color: #6c757d; } /* Secondary */
.status-cancelado { background-color: #343a40; } /* Dark */
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnModificar = document.getElementById('btn-modificar-asistencia');
    const viewModeElements = document.querySelectorAll('.view-mode');
    const editModeElements = document.querySelectorAll('.edit-mode');

    btnModificar.addEventListener('click', function() {
        viewModeElements.forEach(el => el.style.display = 'none');
        editModeElements.forEach(el => el.style.display = ''); // Revert to default display
        this.style.display = 'none'; // Hide the modify button itself
    });
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
