<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Mantenimiento de Clientes</h1>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="info-message">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <h2><?php echo isset($cliente_a_editar) ? 'Editar Cliente' : 'Nuevo Cliente'; ?></h2>
    <form action="index.php?view=clientes" method="POST">

        <?php if (isset($cliente_a_editar)): ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id_cliente" value="<?php echo $cliente_a_editar['id_cliente']; ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="create">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="nombres">Nombres:</label>
                <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($cliente_a_editar['nombres'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($cliente_a_editar['apellidos'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="id_tipo_documento">Tipo de Documento:</label>
                <select id="id_tipo_documento" name="id_tipo_documento" required>
                    <option value="1" <?php echo (isset($cliente_a_editar) && $cliente_a_editar['id_tipo_documento'] == 1) ? 'selected' : ''; ?>>DNI</option>
                    <option value="2" <?php echo (isset($cliente_a_editar) && $cliente_a_editar['id_tipo_documento'] == 2) ? 'selected' : ''; ?>>Pasaporte</option>
                    <option value="3" <?php echo (isset($cliente_a_editar) && $cliente_a_editar['id_tipo_documento'] == 3) ? 'selected' : ''; ?>>Carnet de Extranjería</option>
                </select>
            </div>
            <div class="form-group">
                <label for="numero_documento">Número de Documento:</label>
                <input type="text" id="numero_documento" name="numero_documento" value="<?php echo htmlspecialchars($cliente_a_editar['numero_documento'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-row">
             <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cliente_a_editar['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente_a_editar['telefono'] ?? ''); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="codigo_erp">Código ERP:</label>
            <input type="text" id="codigo_erp" name="codigo_erp" value="<?php echo htmlspecialchars($cliente_a_editar['codigo_erp'] ?? ''); ?>">
        </div>

        <div class="form-actions">
            <?php if (isset($cliente_a_editar)): ?>
                <a href="index.php?view=clientes" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary"><?php echo isset($cliente_a_editar) ? 'Actualizar Cliente' : 'Crear Cliente'; ?></button>
        </div>
    </form>
</div>

<h2>Lista de Clientes</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombres y Apellidos</th>
            <th>Documento</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?php echo $cliente['id_cliente']; ?></td>
                <td><?php echo htmlspecialchars($cliente['apellidos'] . ', ' . $cliente['nombres']); ?></td>
                <td><?php echo htmlspecialchars($cliente['tipo_documento']) . ': ' . htmlspecialchars($cliente['numero_documento']); ?></td>
                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                <td>
                    <a href="index.php?view=clientes&action=edit&id_cliente=<?php echo $cliente['id_cliente']; ?>" class="btn btn-warning">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
