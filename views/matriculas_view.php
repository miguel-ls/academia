<?php
// =================================================================
// Vista para el Listado de Matrículas
// =================================================================
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Matrículas - <?php echo SITE_NAME; ?></title>
    <style>
        body { font-family: sans-serif; }
        .header { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background-color: #fff; border-bottom: 1px solid #ddd; }
        .header h1 { margin: 0; }
        .new-btn { padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; }
        .new-btn:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <?php // include 'partials/header.php'; ?>
    <a href="index.php?view=dashboard" style="display: block; padding: 10px;">Volver al Panel</a>
    <hr>

    <div class="header">
        <h1>Gestión de Matrículas</h1>
        <a href="index.php?view=matriculas&action=nueva" class="new-btn">Nueva Matrícula</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Matrícula</th>
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
                        <td><?php echo htmlspecialchars($matricula['estado']); ?></td>
                        <td><?php echo htmlspecialchars($matricula['registrado_por']); ?></td>
                        <td>
                            <a href="index.php?view=matriculas&action=ver_detalle&id=<?php echo $matricula['id_matricula']; ?>">Ver Detalle</a>
                            <?php if ($matricula['estado'] === 'Activa'): ?>
                                <form action="index.php?view=matriculas" method="POST" style="display:inline;" class="form-anular">
                                    <input type="hidden" name="action" value="anular">
                                    <input type="hidden" name="id_matricula" value="<?php echo $matricula['id_matricula']; ?>">
                                    <input type="hidden" name="observaciones" class="observaciones-input">
                                    <button type="submit" class="btn-anular">Anular</button>
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
                    if (observaciones !== null) { // Si el usuario no presiona "Cancelar" en el prompt
                        form.querySelector('.observaciones-input').value = observaciones;
                        form.submit();
                    }
                }
            });
        });
    });
    </script>
</body>
</html>
