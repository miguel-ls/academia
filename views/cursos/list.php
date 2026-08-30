<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Cursos</h1>
    </div>
    <div class="page-header-right">
        <form action="index.php?view=cursos&amp;action=migrate_classification" method="POST" style="display: inline;">
            <button type="submit" class="btn btn-success" onclick="return confirm('¿Desea migrar la clasificación desde el sistema origen?');">1. Migrar Clasificacion</button>
        </form>
        <form action="index.php?view=cursos&amp;action=migrate" method="POST" style="display: inline;">
            <button type="submit" class="btn btn-success" onclick="return confirm('¿Desea migrar los cursos desde el sistema origen?');">2. Migrar Cursos</button>
        </form>
        <a href="index.php?view=cursos&action=new" class="btn btn-primary">Nuevo Curso</a>
    </div>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form action="index.php?view=cursos" method="GET">
        <input type="hidden" name="view" value="cursos">
        <input type="text" name="search" placeholder="Buscar por nombre, descripción o código..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
        <button type="submit" class="btn">Buscar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Código ERP</th>
            <th>Nombre</th>
            <th>Tipo de Curso</th>
            <th>Descripción</th>
            
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($cursos)): ?>
            <tr>
                <td colspan="6">No se encontraron cursos.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($cursos as $curso): ?>
                <tr>
                    <td><?php echo $curso['id_curso']; ?></td>
                    <td><?php echo htmlspecialchars($curso['codigo_erp']); ?></td>
                    <td><?php echo htmlspecialchars($curso['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($curso['tipo_curso']); ?></td>
                    <td><?php echo htmlspecialchars($curso['descripcion']); ?></td>
                    
                    <td>
                        <a href="index.php?view=cursos&action=edit&id=<?php echo $curso['id_curso']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=cursos&action=delete&id=<?php echo $curso['id_curso']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este curso?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
