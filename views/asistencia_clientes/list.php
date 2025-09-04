<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Asistencia de Clientes</h1>
    </div>
</div>

<?php if (!empty($feedback_message)): ?>
    <div class="info-message <?php echo strpos($feedback_message, 'Error') === 0 ? 'error-message' : ''; ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>

<!-- Search Filters -->
<div class="card">
    <form action="index.php" method="GET">
        <input type="hidden" name="view" value="asistencia_clientes">
        <div class="form-row">
            <div class="form-group">
                <label for="filtro_cliente">Cliente:</label>
                <select id="filtro_cliente" name="filtro_cliente">
                    <option value="">Todos</option>
                    <?php foreach ($lista_clientes as $cliente): ?>
                        <option value="<?php echo $cliente['id_cliente']; ?>" <?php echo (($_GET['filtro_cliente'] ?? '') == $cliente['id_cliente']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cliente['apellidos'] . ', ' . $cliente['nombres']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="filtro_curso">Curso:</label>
                <select id="filtro_curso" name="filtro_curso">
                    <option value="">Todos</option>
                     <?php foreach ($lista_cursos as $curso): ?>
                        <option value="<?php echo $curso['id_curso']; ?>" <?php echo (($_GET['filtro_curso'] ?? '') == $curso['id_curso']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($curso['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="filtro_fecha_inicio">Desde:</label>
                <input type="date" id="filtro_fecha_inicio" name="filtro_fecha_inicio" value="<?php echo htmlspecialchars($_GET['filtro_fecha_inicio'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="filtro_fecha_fin">Hasta:</label>
                <input type="date" id="filtro_fecha_fin" name="filtro_fecha_fin" value="<?php echo htmlspecialchars($_GET['filtro_fecha_fin'] ?? ''); ?>">
            </div>
        </div>
        <div class="form-actions">
            <a href="index.php?view=asistencia_clientes" class="btn btn-secondary">Limpiar</a>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>
</div>


<table class="table">
    <thead>
        <tr>
            <th>ID Mat.</th>
            <th>ID Det.</th>
            <th>Cliente</th>
            <th>Curso</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Días</th>
            <th>Horas</th>
            <th>Ubicación</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($matriculas)): ?>
            <tr>
                <td colspan="11">No hay matrículas para mostrar.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($matriculas as $matricula): ?>
                <tr>
                    <td><?php echo $matricula['id_matricula']; ?></td>
                    <td><?php echo $matricula['id_matricula_detalle']; ?></td>
                    <td><?php echo htmlspecialchars($matricula['cliente_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($matricula['curso_nombre']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($matricula['fecha_inicio'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($matricula['fecha_fin'])); ?></td>
                    <td><?php echo htmlspecialchars($matricula['dias']); ?></td>
                    <td><?php echo htmlspecialchars($matricula['horas']); ?></td>
                    <td><?php echo htmlspecialchars($matricula['ubicacion']); ?></td>
                    <td>
                        <span class="badge status-<?php echo strtolower(htmlspecialchars($matricula['estado'])); ?>">
                            <?php echo htmlspecialchars($matricula['estado']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="index.php?view=asistencia_clientes&action=marcar&id=<?php echo $matricula['id_matricula_detalle']; ?>" class="btn btn-primary">Marcar Asistencia</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<style>
.badge {
    padding: 5px 10px;
    border-radius: 12px;
    color: #fff;
    font-weight: bold;
    font-size: 0.9em;
    text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
}
.status-activa {
    background-color: #28a745; /* Verde */
}
.status-anulada {
    background-color: #dc3545; /* Rojo */
}
.status-completada {
    background-color: #007bff; /* Azul */
}
</style>

<?php require_once 'views/partials/footer.php'; ?>
