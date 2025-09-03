<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Profesores</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=profesores&action=new" class="btn btn-primary">Nuevo Profesor</a>
    </div>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form action="index.php?view=profesores" method="GET">
        <input type="hidden" name="view" value="profesores">
        <input type="text" name="search" placeholder="Buscar por nombre, apellidos o documento..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
        <button type="submit" class="btn">Buscar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombres y Apellidos</th>
            <th>Documento</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Especialidad / Tipo de Curso</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($profesores)): ?>
            <tr>
                <td colspan="7">No se encontraron profesores.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($profesores as $profesor): ?>
                <tr>
                    <td><?php echo $profesor['id_profesor']; ?></td>
                    <td><?php echo htmlspecialchars($profesor['apellidos'] . ', ' . $profesor['nombres']); ?></td>
                    <td><?php echo htmlspecialchars($profesor['tipo_documento']) . ': ' . htmlspecialchars($profesor['numero_documento']); ?></td>
                    <td><?php echo htmlspecialchars($profesor['email']); ?></td>
                    <td><?php echo htmlspecialchars($profesor['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($profesor['especialidad']); ?></td>
                    <td>
                        <a href="index.php?view=profesores&action=edit&id=<?php echo $profesor['id_profesor']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=profesores&action=delete&id=<?php echo $profesor['id_profesor']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este profesor?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
