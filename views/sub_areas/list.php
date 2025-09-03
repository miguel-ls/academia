<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Sub Áreas</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=sub_areas&action=new" class="btn btn-primary">Nueva Sub Área</a>
    </div>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form action="index.php?view=sub_areas" method="GET">
        <input type="hidden" name="view" value="sub_areas">
        <input type="text" name="search" placeholder="Buscar por descripción, área, etc..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
        <button type="submit" class="btn">Buscar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Área Principal</th>
            <th>Descripción</th>
            <th>Número/Código</th>
            <th>Capacidad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($sub_areas)): ?>
            <tr>
                <td colspan="6">No se encontraron sub áreas.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($sub_areas as $sub_area): ?>
                <tr>
                    <td><?php echo $sub_area['id_sub_area']; ?></td>
                    <td><?php echo htmlspecialchars($sub_area['area_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($sub_area['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($sub_area['numero_sub_area']); ?></td>
                    <td><?php echo htmlspecialchars($sub_area['capacidad_maxima']); ?></td>
                    <td>
                        <a href="index.php?view=sub_areas&action=edit&id=<?php echo $sub_area['id_sub_area']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=sub_areas&action=delete&id=<?php echo $sub_area['id_sub_area']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta sub área?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
