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
                        <a href="index.php?view=matriculas&action=editar&id=<?php echo $matricula['id_matricula']; ?>" class="btn btn-info">Ver/Editar</a>
                        <?php if ($matricula['estado'] === 'Activa'): ?>
                            <form action="index.php?view=matriculas" method="POST" style="display:inline;" class="form-anular">
                                <input type="hidden" name="action" value="anular">
                                <input type="hidden" name="id_matricula" value="<?php echo $matricula['id_matricula']; ?>">
                                <input type="hidden" name="observaciones" class="observaciones-input">
                                <button type="button" class="btn btn-warning btn-anular">Anular</button>
                            </form>
                        <?php elseif ($matricula['estado'] === 'Anulada'): ?>
                            <form action="index.php?view=matriculas" method="POST" style="display:inline;" class="form-revertir">
                                <input type="hidden" name="action" value="revertir_anulacion">
                                <input type="hidden" name="id_matricula" value="<?php echo $matricula['id_matricula']; ?>">
                                <button type="button" class="btn btn-success btn-revertir">Revertir</button>
                            </form>
                        <?php endif; ?>

                        <!-- Botón Eliminar -->
                        <form action="index.php?view=matriculas" method="POST" style="display:inline;" class="form-eliminar">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="id_matricula" value="<?php echo $matricula['id_matricula']; ?>">
                            <button type="button" class="btn btn-danger btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handler para Anular
    document.querySelectorAll('.btn-anular').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const confirmacion = confirm('¿Está seguro de que desea ANULAR esta matrícula? Esta acción cambia el estado a "Anulada" y libera las vacantes.');
            if (confirmacion) {
                const observaciones = prompt('Por favor, ingrese un motivo para la anulación:', 'Anulado a petición del cliente.');
                if (observaciones !== null) {
                    form.querySelector('.observaciones-input').value = observaciones;
                    form.submit();
                }
            }
        });
    });

    // Handler para Revertir
    document.querySelectorAll('.btn-revertir').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const confirmacion = confirm('¿Está seguro de que desea REVERTIR la anulación de esta matrícula? Esta acción intentará volver a ocupar las vacantes en los cursos correspondientes.');
            if (confirmacion) {
                form.submit();
            }
        });
    });

    // Handler para Eliminar
    document.querySelectorAll('.btn-eliminar').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const confirmacion = confirm('¡ADVERTENCIA! ¿Está seguro de que desea ELIMINAR PERMANENTEMENTE esta matrícula? Esta acción no se puede deshacer y borrará todos los registros asociados.');
            if (confirmacion) {
                // Doble confirmación para una acción tan destructiva
                const confirmacionFinal = prompt('Para confirmar, por favor escriba la palabra ELIMINAR en mayúsculas:');
                if (confirmacionFinal === 'ELIMINAR') {
                    form.submit();
                } else {
                    alert('La confirmación no es correcta. La acción ha sido cancelada.');
                }
            }
        });
    });
});
</script>

<style>
.badge {
    padding: 5px 10px;
    border-radius: 12px;
    color: #fff;
    font-weight: bold;
    font-size: 0.9em;
    text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
}
.status-activa {
    background-color: #28a745; /* Verde */
}
.status-anulada {
    background-color: #dc3545; /* Rojo */
}
.status-completada {
    background-color: #007bff; /* Azul */
}
</style>

<?php require_once 'views/partials/footer.php'; ?>
