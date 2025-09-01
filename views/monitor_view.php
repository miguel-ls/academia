<?php
// =================================================================
// Vista para el Monitor de Cursos Disponibles
// =================================================================
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor de Cursos - <?php echo SITE_NAME; ?></title>
    <style>
        body { font-family: sans-serif; background-color: #f9f9f9; }
        .header { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background-color: #fff; border-bottom: 1px solid #ddd; }
        .header h1 { margin: 0; }
        .update-btn { padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .update-btn:hover { background-color: #0056b3; }
        .cards-container { display: flex; flex-wrap: wrap; gap: 20px; padding: 20px; }
        .card { background-color: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 300px; padding: 15px; }
        .card h3 { margin-top: 0; color: #0056b3; }
        .card p { margin: 5px 0; }
        .card .label { font-weight: bold; }
        .no-courses { text-align: center; padding: 50px; font-size: 1.2em; color: #777; }
    </style>
</head>
<body>

    <?php // include 'partials/header.php'; ?>
    <a href="index.php?view=dashboard" style="display: block; padding: 10px;">Volver al Panel</a>
    <hr>

    <div class="header">
        <h1>Monitor de Cursos con Vacantes Disponibles</h1>
        <a href="index.php?view=monitor" class="update-btn">Actualizar Listado</a>
    </div>

    <div class="cards-container">
        <?php if (empty($cursos_disponibles)): ?>
            <div class="no-courses">
                <p>No hay cursos con vacantes disponibles en este momento.</p>
            </div>
        <?php else: ?>
            <?php foreach ($cursos_disponibles as $curso): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($curso['nombre_curso']); ?></h3>
                    <p><span class="label">Profesor:</span> <?php echo htmlspecialchars($curso['nombre_profesor']); ?></p>
                    <p><span class="label">Periodo:</span> <?php echo date('d/m/Y', strtotime($curso['fecha_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($curso['fecha_fin'])); ?></p>
                    <p><span class="label">Horario:</span> <?php echo htmlspecialchars($curso['horario_dias']); ?> (<?php echo date('H:i', strtotime($curso['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($curso['hora_fin'])); ?>)</p>
                    <p><span class="label">Ubicación:</span> <?php echo htmlspecialchars($curso['area']); ?> - <?php echo htmlspecialchars($curso['sub_area']); ?></p>
                    <p><span class="label">Vacantes:</span> <?php echo htmlspecialchars($curso['vacantes_disponibles']); ?></p>
                    <p><span class="label">Precio:</span> S/ <?php echo number_format($curso['precio_actual'], 2); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>
