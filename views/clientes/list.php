<?php require_once 'views/partials/header.php'; ?>
<?php $clientes = $clientes ?? []; ?>

<main class="clientes-page">
<div class="page-header clientes-page-header">
    <div class="page-header-left">
        <p class="eyebrow">Directorio comercial</p>
        <h1>Mantenimiento de Clientes</h1>
        <p class="page-subtitle">Administra la informacion y datos de contacto de tus clientes.</p>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=clientes&action=new" class="btn btn-primary clientes-create-button"><span aria-hidden="true">+</span> Nuevo Cliente</a>
    </div>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<section class="clientes-filter-card" aria-label="Buscar clientes">
    <form action="index.php?view=clientes" method="GET">
        <input type="hidden" name="view" value="clientes">
        <div class="filter-heading">
            <span class="filter-icon" aria-hidden="true"></span>
            <div><strong>Buscar clientes</strong><small>Por nombre, apellidos o documento</small></div>
        </div>
        <div class="filter-controls">
            <input type="text" name="search" placeholder="Escribe para buscar..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <?php if (!empty($search_term)): ?>
                <a href="index.php?view=clientes" class="btn btn-secondary filter-clear">Limpiar</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="clientes-table-card">
    <div class="clientes-table-meta">
        <div><h2>Listado de clientes</h2><p><?php echo count($clientes); ?> registro<?php echo count($clientes) === 1 ? '' : 's'; ?> encontrado<?php echo count($clientes) === 1 ? '' : 's'; ?></p></div>
        <span class="table-meta-label">Informacion actualizada</span>
    </div>
    <div class="clientes-table-scroll">
<table class="table clientes-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombres y Apellidos</th>
            <th>Documento</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th>Dirección</th>
            <th>Ubigeo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($clientes)): ?>
            <tr>
                <td colspan="9">No se encontraron clientes.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td class="cliente-id">#<?php echo $cliente['id_cliente']; ?></td>
                    <td class="cliente-name"><strong><?php echo htmlspecialchars($cliente['apellidos'] . ', ' . $cliente['nombres']); ?></strong></td>
                    <td><span class="document-type"><?php echo htmlspecialchars($cliente['tipo_documento']); ?></span><span class="document-number"><?php echo htmlspecialchars($cliente['numero_documento']); ?></span></td>
                    <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                    <td><span class="status-badge status-<?php echo strtolower($cliente['estado']); ?>"><?php echo htmlspecialchars($cliente['estado']); ?></span></td>
                    <td><?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($cliente['codigo_ubigeo'] ?? ''); ?></td>
                    <td class="cliente-actions">
                        <a href="index.php?view=clientes&action=edit&id=<?php echo $cliente['id_cliente']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=clientes&action=delete&id=<?php echo $cliente['id_cliente']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este cliente?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
    </div>
</section>
</main>

<?php require_once 'views/partials/footer.php'; ?>
