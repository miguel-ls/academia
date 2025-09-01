<?php
// =================================================================
// Vista para el Mantenimiento de Clientes
// =================================================================
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento de Clientes - <?php echo SITE_NAME; ?></title>
    <!-- Reutilizamos el mismo estilo simple -->
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .form-container { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; }
        .feedback { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .feedback.success { background-color: #d4edda; color: #155724; }
        .feedback.error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <h1>Mantenimiento de Clientes</h1>
    <p>Gestione los alumnos de la academia.</p>

    <?php if (!empty($feedback_message)): ?>
        <div class="feedback <?php echo strpos($feedback_message, 'Error') === false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para Crear o Editar Clientes -->
    <div class="form-container">
        <h2><?php echo isset($cliente_a_editar) ? 'Editar Cliente' : 'Crear Nuevo Cliente'; ?></h2>
        <form action="index.php?view=clientes" method="POST">

            <?php if (isset($cliente_a_editar)): ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id_cliente" value="<?php echo $cliente_a_editar['id_cliente']; ?>">
            <?php else: ?>
                <input type="hidden" name="action" value="create">
            <?php endif; ?>

            <div>
                <label for="nombres">Nombres:</label>
                <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($cliente_a_editar['nombres'] ?? ''); ?>" required>
            </div>
            <br>
            <div>
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($cliente_a_editar['apellidos'] ?? ''); ?>" required>
            </div>
            <br>
            <div>
                <label for="id_tipo_documento">Tipo de Documento:</label>
                <select id="id_tipo_documento" name="id_tipo_documento" required>
                    <!-- Datos de tipos de documento (cargados de la BD en la versión final) -->
                    <option value="1" <?php echo (isset($cliente_a_editar) && $cliente_a_editar['id_tipo_documento'] == 1) ? 'selected' : ''; ?>>DNI</option>
                    <option value="2" <?php echo (isset($cliente_a_editar) && $cliente_a_editar['id_tipo_documento'] == 2) ? 'selected' : ''; ?>>Pasaporte</option>
                    <option value="3" <?php echo (isset($cliente_a_editar) && $cliente_a_editar['id_tipo_documento'] == 3) ? 'selected' : ''; ?>>Carnet de Extranjería</option>
                </select>
            </div>
            <br>
            <div>
                <label for="numero_documento">Número de Documento:</label>
                <input type="text" id="numero_documento" name="numero_documento" value="<?php echo htmlspecialchars($cliente_a_editar['numero_documento'] ?? ''); ?>" required>
            </div>
            <br>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cliente_a_editar['email'] ?? ''); ?>">
            </div>
            <br>
            <div>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente_a_editar['telefono'] ?? ''); ?>">
            </div>
            <br>
            <div>
                <label for="codigo_erp">Código ERP:</label>
                <input type="text" id="codigo_erp" name="codigo_erp" value="<?php echo htmlspecialchars($cliente_a_editar['codigo_erp'] ?? ''); ?>">
            </div>
            <br>

            <button type="submit"><?php echo isset($cliente_a_editar) ? 'Actualizar Cliente' : 'Crear Cliente'; ?></button>
            <?php if (isset($cliente_a_editar)): ?>
                <a href="index.php?view=clientes">Cancelar Edición</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabla con la lista de clientes -->
    <h2>Lista de Clientes</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Documento</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clientes)): ?>
                <tr>
                    <td colspan="7">No hay clientes registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente['id_cliente']; ?></td>
                        <td><?php echo htmlspecialchars($cliente['nombres']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['tipo_documento']) . ': ' . htmlspecialchars($cliente['numero_documento']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                        <td>
                            <a href="index.php?view=clientes&action=edit&id_cliente=<?php echo $cliente['id_cliente']; ?>">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
