<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Áreas</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=areas&action=new" class="btn btn-primary">Nueva Área</a>
    </div>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form action="index.php?view=areas" method="GET">
        <input type="hidden" name="view" value="areas">
        <input type="text" name="search" placeholder="Buscar por nombre o tipo..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
        <button type="submit" class="btn">Buscar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo de Área</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($areas)): ?>
            <tr>
                <td colspan="4">No se encontraron áreas.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($areas as $area): ?>
                <tr>
                    <td><?php echo $area['id_area']; ?></td>
                    <td><?php echo htmlspecialchars($area['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($area['tipo_area']); ?></td>
                    <td>
                        <a href="index.php?view=areas&action=edit&id=<?php echo $area['id_area']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=areas&action=delete&id=<?php echo $area['id_area']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta área?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
