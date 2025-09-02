<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Mantenimiento de Tipos de Horario</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=tipos_horario&action=new" class="btn btn-primary">Nuevo Tipo de Horario</a>
    </div>
</div>


<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form action="index.php?view=tipos_horario" method="GET">
        <input type="hidden" name="view" value="tipos_horario">
        <input type="text" name="search" placeholder="Buscar por descripción..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
        <button type="submit" class="btn">Buscar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th>Días de la Semana</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $dias_map = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        ?>
        <?php if (empty($tipos_horario)): ?>
            <tr>
                <td colspan="4">No se encontraron tipos de horario.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($tipos_horario as $tipo): ?>
                <tr>
                    <td><?php echo $tipo['id_tipo_horario']; ?></td>
                    <td><?php echo htmlspecialchars($tipo['descripcion']); ?></td>
                    <td>
                        <?php
                        $dias_numeros = explode(',', $tipo['dias_semana']);
                        $dias_texto = array_map(function($num) use ($dias_map) {
                            return $dias_map[(int)$num] ?? '';
                        }, $dias_numeros);
                        echo htmlspecialchars(implode(', ', array_filter($dias_texto)));
                        ?>
                    </td>
                    <td>
                        <a href="index.php?view=tipos_horario&action=edit&id=<?php echo $tipo['id_tipo_horario']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=tipos_horario&action=delete&id=<?php echo $tipo['id_tipo_horario']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este tipo de horario?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
