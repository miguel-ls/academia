<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Formas de Pago</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=formas_pago&action=new" class="btn btn-primary">Nueva Forma de Pago</a>
    </div>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form action="index.php?view=formas_pago" method="GET">
        <input type="hidden" name="view" value="formas_pago">
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
        <?php if (empty($formas_pago)): ?>
            <tr>
                <td colspan="3">No se encontraron formas de pago.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($formas_pago as $forma): ?>
                <tr>
                    <td><?php echo $forma['id_forma_pago']; ?></td>
                    <td><?php echo htmlspecialchars($forma['nombre']); ?></td>
                    <td>
                        <a href="index.php?view=formas_pago&action=edit&id=<?php echo $forma['id_forma_pago']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=formas_pago&action=delete&id=<?php echo $forma['id_forma_pago']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta forma de pago?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
