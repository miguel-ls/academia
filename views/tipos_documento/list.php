<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Tipos de Documento</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=tipos_documento&action=new" class="btn btn-primary">Nuevo Tipo de Documento</a>
    </div>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form action="index.php?view=tipos_documento" method="GET">
        <input type="hidden" name="view" value="tipos_documento">
        <input type="text" name="search" placeholder="Buscar por descripción o código..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
        <button type="submit" class="btn">Buscar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th>Longitud</th>
            <th>Código SUNAT</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tipos_documento)): ?>
            <tr>
                <td colspan="5">No se encontraron tipos de documento.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($tipos_documento as $tipo): ?>
                <tr>
                    <td><?php echo $tipo['id_tipo_documento']; ?></td>
                    <td><?php echo htmlspecialchars($tipo['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($tipo['longitud']); ?></td>
                    <td><?php echo htmlspecialchars($tipo['codigo_sunat']); ?></td>
                    <td>
                        <a href="index.php?view=tipos_documento&action=edit&id=<?php echo $tipo['id_tipo_documento']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=tipos_documento&action=delete&id=<?php echo $tipo['id_tipo_documento']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este tipo de documento?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
