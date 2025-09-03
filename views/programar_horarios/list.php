<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Programación de Horarios</h1>
    </div>
    <div class="page-header-right">
        <a href="index.php?view=programar_horarios&action=new" class="btn btn-primary">Nueva Programación</a>
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
        <input type="hidden" name="view" value="programar_horarios">
        <div class="form-row">
            <div class="form-group">
                <label for="filtro_profesor">Profesor:</label>
                <select id="filtro_profesor" name="filtro_profesor">
                    <option value="">Todos</option>
                    <?php if (!empty($lista_profesores)): ?>
                        <?php foreach ($lista_profesores as $profesor): ?>
                            <option value="<?php echo $profesor['id_profesor']; ?>" <?php echo (($_GET['filtro_profesor'] ?? '') == $profesor['id_profesor']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($profesor['apellidos'] . ', ' . $profesor['nombres']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="filtro_curso">Curso:</label>
                <select id="filtro_curso" name="filtro_curso">
                    <option value="">Todos</option>
                    <?php if (!empty($lista_cursos)): ?>
                        <?php foreach ($lista_cursos as $curso): ?>
                            <option value="<?php echo $curso['id_curso']; ?>" <?php echo (($_GET['filtro_curso'] ?? '') == $curso['id_curso']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($curso['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
            <a href="index.php?view=programar_horarios" class="btn btn-secondary">Limpiar</a>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>
</div>


<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Curso</th>
            <th>Profesor</th>
            <th>Ubicación</th>
            <th>Tipo de Horario</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Hora Inicio</th>
            <th>Hora Fin</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($programaciones)): ?>
            <tr>
                <td colspan="11">No se encontraron programaciones.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($programaciones as $prog): ?>
                <tr>
                    <td><?php echo $prog['id_curso_programado']; ?></td>
                    <td><?php echo htmlspecialchars($prog['curso_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($prog['profesor_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($prog['ubicacion']); ?></td>
                    <td><?php echo htmlspecialchars($prog['tipo_horario_nombre']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($prog['fecha_inicio'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($prog['fecha_fin'])); ?></td>
                    <td><?php echo date('h:i A', strtotime($prog['hora_inicio'])); ?></td>
                    <td><?php echo date('h:i A', strtotime($prog['hora_fin'])); ?></td>
                    <td>
                        <span class="badge status-<?php echo strtolower($prog['estado']); ?>">
                            <?php echo htmlspecialchars($prog['estado']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="index.php?view=programar_horarios&action=edit&id=<?php echo $prog['id_curso_programado']; ?>" class="btn btn-warning">Editar</a>
                        <a href="index.php?view=programar_horarios&action=delete&id=<?php echo $prog['id_curso_programado']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta programación? Esta acción no se puede deshacer.');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/partials/footer.php'; ?>
