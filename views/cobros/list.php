<?php
$feedback_message = $feedback_message ?? ($_SESSION['feedback_message'] ?? '');
if (isset($_SESSION['feedback_message'])) {
    unset($_SESSION['feedback_message']);
}
$error_message = $error_message ?? $feedback_message;
$cobros = $cobros ?? [];
require_once 'views/partials/header.php';
?>

<div class="cobros-page">
    <div class="page-header">
        <div class="page-header-left">
            <h1>Gestión de Cobros</h1>
        </div>
        <div class="page-header-right">
            <a href="index.php?view=cobros&action=new" class="btn btn-primary">Nuevo Cobro</a>
        </div>
    </div>

<?php if (!empty($feedback_message)): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        window.showMessageModal && window.showMessageModal(<?php echo json_encode($feedback_message, JSON_UNESCAPED_UNICODE); ?>, { title: 'Mensaje' });
    });
    </script>
<?php endif; ?>

<form method="GET" action="index.php" class="cobros-filters">
    <input type="hidden" name="view" value="cobros">
    <div class="filter-field cobros-client-filter">
        <label for="filtro_cliente">Cliente</label>
        <div class="filter-autocomplete">
            <input id="filtro_cliente" class="form-control" autocomplete="off" placeholder="Escriba para buscar cliente...">
            <input id="cliente_id" name="cliente_id" type="hidden" value="<?php echo (int)($_GET['cliente_id'] ?? 0) ?: ''; ?>">
            <div id="clientes-filtro-resultados" class="filter-results"></div>
        </div>
    </div>
    <div class="filter-field cobros-course-filter">
        <label for="filtro_curso">Curso</label>
        <div class="filter-autocomplete">
            <input id="filtro_curso" class="form-control" autocomplete="off" placeholder="Escriba para buscar curso...">
            <input id="curso_id" name="curso_id" type="hidden" value="<?php echo (int)($_GET['curso_id'] ?? 0) ?: ''; ?>">
            <div id="cursos-filtro-resultados" class="filter-results"></div>
        </div>
    </div>
    <div class="filter-field cobros-location-filter">
        <label for="filtro_ubicacion">Ubicación</label>
        <div class="filter-autocomplete">
            <input id="filtro_ubicacion" class="form-control" autocomplete="off" placeholder="Escriba para buscar ubicación...">
            <input id="ubicacion_id" name="ubicacion_id" type="hidden" value="<?php echo (int)($_GET['ubicacion_id'] ?? 0) ?: ''; ?>">
            <div id="ubicaciones-filtro-resultados" class="filter-results"></div>
        </div>
    </div>
    <div class="filter-field cobros-professor-filter">
        <label for="filtro_profesor">Profesor</label>
        <div class="filter-autocomplete">
            <input id="filtro_profesor" class="form-control" autocomplete="off" placeholder="Escriba para buscar profesor...">
            <input id="profesor_id" name="profesor_id" type="hidden" value="<?php echo (int)($_GET['profesor_id'] ?? 0) ?: ''; ?>">
            <div id="profesores-filtro-resultados" class="filter-results"></div>
        </div>
    </div>
    <div class="filter-field cobros-schedule-filter">
        <label for="filtro_horario">Horarios y horas</label>
        <div class="filter-autocomplete">
            <input id="filtro_horario" class="form-control" autocomplete="off" placeholder="Escriba para buscar horario...">
            <input id="horario_id" name="horario_id" type="hidden" value="<?php echo (int)($_GET['horario_id'] ?? 0) ?: ''; ?>">
            <div id="horarios-filtro-resultados" class="filter-results"></div>
        </div>
    </div>
    <div class="filter-field">
        <label for="fecha_cobro">Fecha</label>
        <input type="date" id="fecha_cobro" name="fecha_cobro" class="form-control" value="<?php echo htmlspecialchars($_GET['fecha_cobro'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="filter-field">
        <label for="numero_matricula">Número de matrícula</label>
        <input type="number" id="numero_matricula" name="numero_matricula" class="form-control" min="1" value="<?php echo htmlspecialchars($_GET['numero_matricula'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="filter-field">
        <label for="numero_operacion">Número de operación</label>
        <input type="text" id="numero_operacion" name="numero_operacion" class="form-control" value="<?php echo htmlspecialchars($_GET['numero_operacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <a href="index.php?view=cobros" class="btn btn-secondary">Limpiar</a>
    </div>
</form>

<table class="cobros-table table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Matrícula</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Forma de pago</th>
            <th>N. operación</th>
            <th>Importe</th>
            <th>Observaciones</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($cobros)): ?>
            <tr>
                <td colspan="9" style="text-align: center;">No hay cobros registrados.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($cobros as $cobro): ?>
                <tr>
                    <td><?php echo (int)$cobro['id_cobro']; ?></td>
                    <td><?php echo (int)$cobro['id_matricula']; ?></td>
                    <td class="cobros-alumnos-cursos"><?php echo nl2br(htmlspecialchars($cobro['alumnos_cursos'] ?? '', ENT_QUOTES, 'UTF-8')); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($cobro['fecha_cobro'])); ?></td>
                    <td><?php echo htmlspecialchars($cobro['forma_pago'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($cobro['numero_operacion'] ?? ''); ?></td>
                    <td>S/ <?php echo number_format((float)($cobro['importe'] ?? 0), 2); ?></td>
                    <td><?php echo htmlspecialchars($cobro['observaciones'] ?? ''); ?></td>
                    <td>
                        <a href="index.php?view=cobros&action=ver&id=<?php echo (int)$cobro['id_cobro']; ?>" class="btn btn-info">Ver</a>
                        <a href="index.php?view=cobros&action=edit&id=<?php echo (int)$cobro['id_cobro']; ?>" class="btn btn-warning">Editar</a>
                        <button type="button" class="btn btn-danger btn-delete-cobro" data-id="<?php echo (int)$cobro['id_cobro']; ?>">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<form id="delete-cobro-form" method="POST" action="index.php?view=cobros" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="delete-cobro-id">
</form>

<?php require_once 'views/partials/modal_error.php'; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function configurarAutocompletado(inputId, hiddenId, resultsId, containerSelector, url, idField, label) {
        const input = document.getElementById(inputId);
        const hiddenInput = document.getElementById(hiddenId);
        const results = document.getElementById(resultsId);
        const container = document.querySelector(containerSelector);
        let timeout;

        const closeResults = function () {
            results.classList.remove('is-open');
            results.innerHTML = '';
        };

        input.addEventListener('input', function () {
            hiddenInput.value = '';
            clearTimeout(timeout);
            const query = input.value.trim();
            if (query.length < 2) {
                closeResults();
                return;
            }
            timeout = setTimeout(function () {
                fetch(url + encodeURIComponent(query))
                    .then(function (response) { return response.json(); })
                    .then(function (items) {
                        results.innerHTML = '';
                        if (!items.length) {
                            results.innerHTML = '<div class="filter-empty">No se encontraron resultados.</div>';
                        } else {
                            items.slice(0, 20).forEach(function (item) {
                                const result = document.createElement('button');
                                result.type = 'button';
                                result.className = 'filter-result';
                                result.dataset.id = item[idField];
                                result.textContent = label(item);
                                results.appendChild(result);
                            });
                        }
                        results.classList.add('is-open');
                    })
                    .catch(closeResults);
            }, 300);
        });
        results.addEventListener('click', function (event) {
            const result = event.target.closest('.filter-result');
            if (!result) return;
            input.value = result.textContent;
            hiddenInput.value = result.dataset.id;
            closeResults();
        });
        document.addEventListener('click', function (event) {
            if (!container.contains(event.target)) closeResults();
        });
    }

    configurarAutocompletado('filtro_cliente', 'cliente_id', 'clientes-filtro-resultados', '.cobros-client-filter', 'index.php?view=cobros&action=buscar_cliente_filtro&q=', 'id_cliente', function (item) { return (item.nombres || '') + ' ' + (item.apellidos || ''); });
    configurarAutocompletado('filtro_curso', 'curso_id', 'cursos-filtro-resultados', '.cobros-course-filter', 'index.php?view=cobros&action=buscar_curso_filtro&q=', 'id_curso', function (item) { return item.nombre || ''; });
    configurarAutocompletado('filtro_ubicacion', 'ubicacion_id', 'ubicaciones-filtro-resultados', '.cobros-location-filter', 'index.php?view=cobros&action=buscar_ubicacion_filtro&q=', 'id_sub_area', function (item) { return (item.area_nombre || '') + ' - ' + (item.descripcion || '') + ' ' + (item.numero_sub_area || ''); });
    configurarAutocompletado('filtro_profesor', 'profesor_id', 'profesores-filtro-resultados', '.cobros-professor-filter', 'index.php?view=cobros&action=buscar_profesor_filtro&q=', 'id_profesor', function (item) { return item.nombre_completo || ''; });
    configurarAutocompletado('filtro_horario', 'horario_id', 'horarios-filtro-resultados', '.cobros-schedule-filter', 'index.php?view=cobros&action=buscar_horario_filtro&q=', 'id_curso_programado', function (item) { return item.horario || ''; });

    document.querySelectorAll('.btn-delete-cobro').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            window.showMessageModal('¿Está seguro que desea eliminar este cobro?', {
                title: 'Confirmación',
                onAccept: function () {
                    const form = document.getElementById('delete-cobro-form');
                    const input = document.getElementById('delete-cobro-id');
                    input.value = id;
                    form.submit();
                }
            });
        });
    });
});
</script>
