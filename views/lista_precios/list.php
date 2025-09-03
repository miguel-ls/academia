<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Lista de Precios</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=lista_precios&action=new" class="btn btn-primary">Nuevo Precio</a>
    </div>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form action="index.php?view=lista_precios" method="GET">
        <input type="hidden" name="view" value="lista_precios">
        <input type="text" name="search" placeholder="Buscar por curso o tipo de precio..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
        <button type="submit" class="btn">Buscar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Curso</th>
            <th>Tipo de Precio</th>
            <th>Precio</th>
            <th>Vigencia Inicio</th>
            <th>Vigencia Fin</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($lista_precios)): ?>
            <tr>
                <td colspan="7">No se encontraron precios en la lista.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($lista_precios as $precio_item): ?>
                <tr>
                    <td><?php echo $precio_item['id_lista_precio']; ?></td>
                    <td><?php echo htmlspecialchars($precio_item['curso_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($precio_item['tipo_precio_nombre']); ?></td>
                    <td>S/ <?php echo number_format($precio_item['precio'], 2); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($precio_item['vigencia_inicio'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($precio_item['vigencia_fin'])); ?></td>
                    <td>
                        <a href="index.php?view=lista_precios&action=edit&id=<?php echo $precio_item['id_lista_precio']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=lista_precios&action=delete&id=<?php echo $precio_item['id_lista_precio']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este precio de la lista?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
