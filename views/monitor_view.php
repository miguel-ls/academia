<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Monitor de Cursos con Vacantes Disponibles</h1>
    <div class="page-header-right">
         <a href="index.php?view=monitor" class="btn btn-secondary">Actualizar Listado</a>
    </div>
</div>

<div class="cards-container">
    <?php if (empty($cursos_disponibles)): ?>
        <div class="no-courses" style="width: 100%; text-align: center; padding: 40px; background-color: #fff; border: 1px solid #ddd; border-radius: 5px;">
            <p>No hay cursos con vacantes disponibles en este momento.</p>
        </div>
    <?php else: ?>
        <?php foreach ($cursos_disponibles as $curso): ?>
            <div class="card" style="background-color: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 320px; padding: 15px; margin-bottom: 20px;">
                <h3 style="margin-top: 0; color: #0056b3;"><?php echo htmlspecialchars($curso['nombre_curso']); ?></h3>
                <p><strong>Profesor:</strong> <?php echo htmlspecialchars($curso['nombre_profesor']); ?></p>
                <p><strong>Periodo:</strong> <?php echo date('d/m/Y', strtotime($curso['fecha_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($curso['fecha_fin'])); ?></p>
                <p><strong>Horario:</strong> <?php echo htmlspecialchars($curso['horario_dias']); ?> (<?php echo date('H:i', strtotime($curso['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($curso['hora_fin'])); ?>)</p>
                <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($curso['area'] . ' - ' . $curso['sub_area'] . ' ' . $curso['numero_sub_area']); ?></p>
                <p><strong>Vacantes:</strong> <?php echo htmlspecialchars($curso['vacantes_disponibles']); ?></p>
                <p><strong>Precio:</strong> S/ <?php echo number_format($curso['precio_actual'], 2); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.cards-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
</style>

<?php require_once 'views/partials/footer.php'; ?>
