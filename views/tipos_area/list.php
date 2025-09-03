<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Tipos de Área</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=tipos_area&action=new" class="btn btn-primary">Nuevo Tipo de Área</a>
    </div>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form action="index.php?view=tipos_area" method="GET">
        <input type="hidden" name="view" value="tipos_area">
        <input type="text" name="search" placeholder="Buscar por nombre..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
        <button type="submit" class="btn">Buscar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tipos_area)): ?>
            <tr>
                <td colspan="3">No se encontraron tipos de área.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($tipos_area as $tipo): ?>
                <tr>
                    <td><?php echo $tipo['id_tipo_area']; ?></td>
                    <td><?php echo htmlspecialchars($tipo['nombre']); ?></td>
                    <td>
                        <a href="index.php?view=tipos_area&action=edit&id=<?php echo $tipo['id_tipo_area']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=tipos_area&action=delete&id=<?php echo $tipo['id_tipo_area']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este tipo de área?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
