<?php require_once 'views/partials/header.php'; ?>
<?php $clientes = $clientes ?? []; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Clientes</h1>
    </div>
    <div class="page-header-right">
        <form action="index.php?view=clientes&amp;action=migrate" method="POST" style="display: inline;">
            <button type="submit" class="btn" onclick="return confirm('¿Desea migrar los clientes desde el sistema origen?');">Migrar Clientes</button>
        </form>
        <a href="index.php?view=clientes&action=new" class="btn btn-primary">Nuevo Cliente</a>
    </div>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form action="index.php?view=clientes" method="GET">
        <input type="hidden" name="view" value="clientes">
        <input type="text" name="search" placeholder="Buscar en todos los campos..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
        <button type="submit" class="btn">Buscar</button>
        <?php if (!empty($search_term)): ?>
            <a href="index.php?view=clientes" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>

            <th>CODIGO_ERP</th>

            <th>Nombres y Apellidos</th>
            <th>Documento</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th>Dirección</th>
            <th>Ubigeo</th>
            <th>Observaciones</th>
            
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($clientes)): ?>
            <tr>
                <td colspan="11">No se encontraron clientes.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo $cliente['id_cliente']; ?></td>

                    <td><?php echo htmlspecialchars($cliente['codigo_erp'] ?? ''); ?></td>

                    <td><?php echo htmlspecialchars( $cliente['nombres']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['tipo_documento']) . ': ' . htmlspecialchars($cliente['numero_documento']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['estado']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($cliente['codigo_ubigeo'] ?? ''); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($cliente['observaciones'] ?? '')); ?></td>
                    
                    <td>
                        <a href="index.php?view=clientes&action=edit&id=<?php echo $cliente['id_cliente']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=clientes&action=delete&id=<?php echo $cliente['id_cliente']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este cliente?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
