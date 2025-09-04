<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Asistencia de Profesores</h1>
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
        <input type="hidden" name="view" value="asistencia_profesores">
        <div class="form-row">
            <div class="form-group">
                <label for="filtro_profesor">Profesor:</label>
                <select id="filtro_profesor" name="filtro_profesor">
                    <option value="">Todos</option>
                    <?php foreach ($lista_profesores as $profesor): ?>
                        <option value="<?php echo $profesor['id_profesor']; ?>" <?php echo (($_GET['filtro_profesor'] ?? '') == $profesor['id_profesor']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($profesor['apellidos'] . ', ' . $profesor['nombres']); ?>
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
            <a href="index.php?view=asistencia_profesores" class="btn btn-secondary">Limpiar</a>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>
</div>


<table class="table">
    <thead>
        <tr>
            <th>ID Prog.</th>
            <th>Curso</th>
            <th>Profesor</th>
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
        <?php if (empty($cursos_programados)): ?>
            <tr>
                <td colspan="10">No hay cursos programados para mostrar.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($cursos_programados as $curso): ?>
                <tr>
                    <td><?php echo $curso['id_curso_programado']; ?></td>
                    <td><?php echo htmlspecialchars($curso['curso_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($curso['profesor_nombre']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($curso['fecha_inicio'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($curso['fecha_fin'])); ?></td>
                    <td><?php echo htmlspecialchars($curso['dias']); ?></td>
                    <td><?php echo htmlspecialchars($curso['horas']); ?></td>
                    <td><?php echo htmlspecialchars($curso['ubicacion']); ?></td>
                    <td>
                        <span class="badge status-<?php echo strtolower($curso['estado']); ?>">
                            <?php echo htmlspecialchars($curso['estado']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="index.php?view=asistencia_profesores&action=marcar&id=<?php echo $curso['id_curso_programado']; ?>" class="btn btn-primary">Marcar Asistencia</a>
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
.status-programado {
    background-color: #17a2b8; /* Azul (Info) */
}
.status-en { /* Para "En Curso" */
    background-color: #28a745; /* Verde (Activo) */
}
.status-finalizado {
    background-color: #6c757d; /* Gris (Secundario) */
}
.status-cancelado {
    background-color: #dc3545; /* Rojo (Peligro) */
}
</style>

<?php require_once 'views/partials/footer.php'; ?>
