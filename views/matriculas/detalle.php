<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Detalle de Matrícula #<?php echo htmlspecialchars($matricula['id_matricula']); ?></h1>
    <a href="index.php?view=matriculas" class="btn btn-secondary">Volver a la Lista</a>
</div>

<!-- Main Enrollment Details -->
<div class="card">
    <h2>Información Principal</h2>
    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($matricula['nombre_cliente']); ?></p>
    <p><strong>Fecha de Matrícula:</strong> <?php echo date('d/m/Y', strtotime($matricula['fecha_matricula'])); ?></p>
    <p><strong>Forma de Pago:</strong> <?php echo htmlspecialchars($matricula['forma_pago']); ?></p>
    <p><strong>Total Original:</strong> S/ <?php echo number_format($matricula['monto_total'], 2); ?></p>
    <p><strong>Descuento Total:</strong> S/ <?php echo number_format($matricula['descuento_total'], 2); ?></p>
    <p><strong>MONTO FINAL:</strong> S/ <?php echo number_format($matricula['monto_final'], 2); ?></p>
    <p><strong>Estado:</strong> <span class="badge status-<?php echo strtolower($matricula['estado']); ?>"><?php echo htmlspecialchars($matricula['estado']); ?></span></p>
    <p><strong>Observaciones:</strong> <?php echo nl2br(htmlspecialchars($matricula['observaciones'])); ?></p>
</div>

<!-- Enrolled Courses Grid -->
<div class="card">
    <h2>Cursos en esta Matrícula</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID Detalle</th>
                <th>Curso</th>
                <th>Alumno Asistente</th>
                <th>Precio Pactado</th>
                <th>Descuento</th>
                <th>Precio Final</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($detalles)): ?>
                <tr>
                    <td colspan="7">No hay cursos en esta matrícula.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($detalles as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle['id_matricula_detalle']; ?></td>
                        <td><?php echo htmlspecialchars($detalle['nombre_curso']); ?></td>
                        <td><?php echo htmlspecialchars($detalle['nombre_cliente_asistencia']); ?></td>
                        <td>S/ <?php echo number_format($detalle['precio_pactado'], 2); ?></td>
                        <td>S/ <?php echo number_format($detalle['descuento'], 2); ?></td>
                        <td>S/ <?php echo number_format($detalle['precio_final'], 2); ?></td>
                        <td>
                            <?php if ($matricula['estado'] === 'Activa'): ?>
                                <form action="index.php?view=matriculas&action=eliminar_detalle" method="POST" class="form-quitar-curso">
                                    <input type="hidden" name="id_matricula" value="<?php echo $matricula['id_matricula']; ?>">
                                    <input type="hidden" name="id_matricula_detalle" value="<?php echo $detalle['id_matricula_detalle']; ?>">
                                    <button type="submit" class="btn btn-danger">Quitar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.form-quitar-curso').forEach(form => {
        form.addEventListener('submit', function(e) {
            const confirmacion = confirm('¿Está seguro de que desea QUITAR este curso de la matrícula? Esta acción recalculará el monto total.');
            if (!confirmacion) {
                e.preventDefault();
            }
        });
    });
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
