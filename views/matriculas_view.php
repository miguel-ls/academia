<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Gestión de Matrículas</h1>
    <a href="index.php?view=matriculas&action=nueva" class="btn btn-success">Nueva Matrícula</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Fecha de Matrícula</th>
            <th>Monto Final</th>
            <th>Estado</th>
            <th>Registrado Por</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($matriculas)): ?>
            <tr>
                <td colspan="7" style="text-align: center;">No hay matrículas registradas.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($matriculas as $matricula): ?>
                <tr>
                    <td><?php echo $matricula['id_matricula']; ?></td>
                    <td><?php echo htmlspecialchars($matricula['nombre_cliente']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($matricula['fecha_matricula'])); ?></td>
                    <td>S/ <?php echo number_format($matricula['monto_final'], 2); ?></td>
                    <td>
                        <span class="badge status-<?php echo strtolower($matricula['estado']); ?>">
                            <?php echo htmlspecialchars($matricula['estado']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($matricula['registrado_por']); ?></td>
                    <td>
                        <a href="index.php?view=matriculas&action=ver_detalle&id=<?php echo $matricula['id_matricula']; ?>" class="btn btn-info">Ver Detalle</a>
                        <?php if ($matricula['estado'] === 'Activa'): ?>
                            <form action="index.php?view=matriculas" method="POST" style="display:inline;" class="form-anular">
                                <input type="hidden" name="action" value="anular">
                                <input type="hidden" name="id_matricula" value="<?php echo $matricula['id_matricula']; ?>">
                                <input type="hidden" name="observaciones" class="observaciones-input">
                                <button type="submit" class="btn btn-danger">Anular</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.form-anular');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const confirmacion = confirm('¿Está seguro de que desea ANULAR esta matrícula? Esta acción no se puede deshacer y devolverá las vacantes al curso.');
            if (confirmacion) {
                const observaciones = prompt('Por favor, ingrese un motivo para la anulación:', 'Anulado a petición del cliente.');
                if (observaciones !== null) {
                    form.querySelector('.observaciones-input').value = observaciones;
                    form.submit();
                }
            }
        });
    });
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
