<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Tipos de Curso</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=tipos_curso&action=new" class="btn btn-primary">Nuevo Tipo de Curso</a>
    </div>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form action="index.php?view=tipos_curso" method="GET">
        <input type="hidden" name="view" value="tipos_curso">
        <input type="text" name="search" placeholder="Buscar por nombre o descripción..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
        <button type="submit" class="btn">Buscar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tipos_curso)): ?>
            <tr>
                <td colspan="4">No se encontraron tipos de curso.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($tipos_curso as $tipo): ?>
                <tr>
                    <td><?php echo $tipo['id_tipo_curso']; ?></td>
                    <td><?php echo htmlspecialchars($tipo['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($tipo['descripcion']); ?></td>
                    <td>
                        <a href="index.php?view=tipos_curso&action=edit&id=<?php echo $tipo['id_tipo_curso']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=tipos_curso&action=delete&id=<?php echo $tipo['id_tipo_curso']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este tipo de curso?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
