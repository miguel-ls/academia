<?php
$filtro_cliente_id = (int)($filtro_cliente_id ?? 0);
$filtro_curso_id = (int)($filtro_curso_id ?? 0);
$filtro_ubicacion_id = (int)($filtro_ubicacion_id ?? 0);
$filtro_profesor_id = (int)($filtro_profesor_id ?? 0);
$filtro_horario_id = (int)($filtro_horario_id ?? 0);
$filtro_fecha_inicio = $filtro_fecha_inicio ?? '';
$filtro_fecha_fin = $filtro_fecha_fin ?? '';
$filtro_estado = $filtro_estado ?? '';
require_once 'views/partials/header.php';
?>

<div class="page-header">
    <h1>Gestión de Matrículas</h1>
    <a href="index.php?view=matriculas&action=nueva" class="btn btn-success">Nueva Matrícula</a>
</div>

<form method="GET" action="index.php" class="matricula-filters">
    <input type="hidden" name="view" value="matriculas">
    <div class="filter-field client-filter-field">
        <label for="filtro_cliente">Cliente</label>
        <div class="filter-autocomplete">
            <input type="text" id="filtro_cliente" class="form-control" autocomplete="off" placeholder="Escriba para buscar cliente..." aria-autocomplete="list" aria-controls="clientes-filtro-resultados">
            <input type="hidden" id="filtro_cliente_id" name="cliente_id" value="<?php echo $filtro_cliente_id ?: ''; ?>">
            <div id="clientes-filtro-resultados" class="filter-results" role="listbox"></div>
        </div>
    </div>
    <div class="filter-field course-filter-field">
        <label for="filtro_curso">Curso</label>
        <div class="filter-autocomplete">
            <input type="text" id="filtro_curso" class="form-control" autocomplete="off" placeholder="Escriba para buscar curso..." aria-autocomplete="list" aria-controls="cursos-filtro-resultados">
            <input type="hidden" id="filtro_curso_id" name="curso_id" value="<?php echo $filtro_curso_id ?: ''; ?>">
            <div id="cursos-filtro-resultados" class="filter-results" role="listbox"></div>
        </div>
    </div>
    <div class="filter-field location-filter-field">
        <label for="filtro_ubicacion">Ubicación</label>
        <div class="filter-autocomplete">
            <input type="text" id="filtro_ubicacion" class="form-control" autocomplete="off" placeholder="Escriba para buscar ubicación..." aria-autocomplete="list" aria-controls="ubicaciones-filtro-resultados">
            <input type="hidden" id="filtro_ubicacion_id" name="ubicacion_id" value="<?php echo $filtro_ubicacion_id ?: ''; ?>">
            <div id="ubicaciones-filtro-resultados" class="filter-results" role="listbox"></div>
        </div>
    </div>
    <div class="filter-field professor-filter-field">
        <label for="filtro_profesor">Profesor</label>
        <div class="filter-autocomplete">
            <input type="text" id="filtro_profesor" class="form-control" autocomplete="off" placeholder="Escriba para buscar profesor..." aria-autocomplete="list" aria-controls="profesores-filtro-resultados">
            <input type="hidden" id="filtro_profesor_id" name="profesor_id" value="<?php echo $filtro_profesor_id ?: ''; ?>">
            <div id="profesores-filtro-resultados" class="filter-results" role="listbox"></div>
        </div>
    </div>
    <div class="filter-field schedule-filter-field">
        <label for="filtro_horario">Horarios y horas</label>
        <div class="filter-autocomplete">
            <input type="text" id="filtro_horario" class="form-control" autocomplete="off" placeholder="Escriba para buscar horario..." aria-autocomplete="list" aria-controls="horarios-filtro-resultados">
            <input type="hidden" id="filtro_horario_id" name="horario_id" value="<?php echo $filtro_horario_id ?: ''; ?>">
            <div id="horarios-filtro-resultados" class="filter-results" role="listbox"></div>
        </div>
    </div>
    <div class="filter-field">
        <label for="fecha_inicio">Fecha inicial</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars($filtro_fecha_inicio, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="filter-field">
        <label for="fecha_fin">Fecha final</label>
        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?php echo htmlspecialchars($filtro_fecha_fin, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="filter-field">
        <label for="estado">Estado</label>
        <select id="estado" name="estado" class="form-control">
            <option value="">Todos</option>
            <option value="Activa" <?php echo $filtro_estado === 'Activa' ? 'selected' : ''; ?>>Activa</option>
            <option value="Anulada" <?php echo $filtro_estado === 'Anulada' ? 'selected' : ''; ?>>Anulada</option>
            <option value="Completada" <?php echo $filtro_estado === 'Completada' ? 'selected' : ''; ?>>Completada</option>
        </select>
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="index.php?view=matriculas" class="btn btn-secondary">Limpiar</a>
    </div>
</form>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Fecha de Matrícula</th>
            <th>Monto Final</th>
            <th>Estado</th>
            <th>Registrado Por</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($matriculas)): ?>
            <tr>
                <td colspan="7" style="text-align: center;">No hay matrículas registradas.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($matriculas as $matricula): ?>
                <tr>
                    <td><?php echo $matricula['id_matricula']; ?></td>
                    <td class="matricula-alumnos-cursos"><?php echo nl2br(htmlspecialchars($matricula['alumnos_cursos'] ?? '', ENT_QUOTES, 'UTF-8')); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($matricula['fecha_matricula'])); ?></td>
                    <td>S/ <?php echo number_format($matricula['monto_final'], 2); ?></td>
                    <td>
                        <span class="badge status-<?php echo strtolower($matricula['estado']); ?>">
                            <?php echo htmlspecialchars($matricula['estado']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($matricula['registrado_por']); ?></td>
                    <td>
                        <a href="index.php?view=matriculas&action=editar&id=<?php echo $matricula['id_matricula']; ?>" class="btn btn-info">Ver/Editar</a>
                        <?php if ($matricula['estado'] === 'Activa'): ?>
                            <form action="index.php?view=matriculas" method="POST" style="display:inline;" class="form-anular">
                                <input type="hidden" name="action" value="anular">
                                <input type="hidden" name="id_matricula" value="<?php echo $matricula['id_matricula']; ?>">
                                <input type="hidden" name="observaciones" class="observaciones-input">
                                <button type="button" class="btn btn-warning btn-anular">Anular</button>
                            </form>
                        <?php elseif ($matricula['estado'] === 'Anulada'): ?>
                            <form action="index.php?view=matriculas" method="POST" style="display:inline;" class="form-revertir">
                                <input type="hidden" name="action" value="revertir_anulacion">
                                <input type="hidden" name="id_matricula" value="<?php echo $matricula['id_matricula']; ?>">
                                <button type="button" class="btn btn-success btn-revertir">Revertir</button>
                            </form>
                        <?php endif; ?>

                        <!-- Botón Eliminar -->
                        <form action="index.php?view=matriculas" method="POST" style="display:inline;" class="form-eliminar">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="id_matricula" value="<?php echo $matricula['id_matricula']; ?>">
                            <button type="button" class="btn btn-danger btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handler para Anular
    document.querySelectorAll('.btn-anular').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const confirmacion = confirm('¿Está seguro de que desea ANULAR esta matrícula? Esta acción cambia el estado a "Anulada" y libera las vacantes.');
            if (confirmacion) {
                const observaciones = prompt('Por favor, ingrese un motivo para la anulación:', 'Anulado a petición del cliente.');
                if (observaciones !== null) {
                    form.querySelector('.observaciones-input').value = observaciones;
                    form.submit();
                }
            }
        });
    });

    // Handler para Revertir
    document.querySelectorAll('.btn-revertir').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const confirmacion = confirm('¿Está seguro de que desea REVERTIR la anulación de esta matrícula? Esta acción intentará volver a ocupar las vacantes en los cursos correspondientes.');
            if (confirmacion) {
                form.submit();
            }
        });
    });

    // Handler para Eliminar
    document.querySelectorAll('.btn-eliminar').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const confirmacion = confirm('¡ADVERTENCIA! ¿Está seguro de que desea ELIMINAR PERMANENTEMENTE esta matrícula? Esta acción no se puede deshacer y borrará todos los registros asociados.');
            if (confirmacion) {
                // Doble confirmación para una acción tan destructiva
                const confirmacionFinal = prompt('Para confirmar, por favor escriba la palabra ELIMINAR en mayúsculas:');
                if (confirmacionFinal === 'ELIMINAR') {
                    form.submit();
                } else {
                    alert('La confirmación no es correcta. La acción ha sido cancelada.');
                }
            }
        });
    });
});
</script>

<style>
.matricula-filters {
    display: grid;
    grid-template-columns: repeat(5, minmax(190px, 1fr));
    gap: 14px;
    align-items: end;
    margin-bottom: 22px;
    padding: 18px;
    border: 1px solid #dbe4ec;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 3px 12px rgba(15, 31, 48, .06);
}
.filter-field {
    min-width: 0;
}
.filter-field label {
    display: block;
    margin-bottom: 6px;
    color: #52677a;
    font-size: .82rem;
    font-weight: 700;
}
.filter-field .form-control {
    width: 100%;
    min-height: 40px;
    padding: 9px 11px;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    background: #fff;
    color: #243b53;
    box-sizing: border-box;
}
.filter-field .form-control:focus {
    outline: 3px solid rgba(37, 99, 235, .14);
    border-color: #2563eb;
}
.filter-autocomplete {
    position: relative;
}
.filter-results {
    position: absolute;
    z-index: 30;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    display: none;
    max-height: 230px;
    overflow-y: auto;
    padding: 5px;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    background: #fff;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .14);
}
.filter-results.is-open {
    display: block;
}
.filter-result {
    display: block;
    width: 100%;
    padding: 9px 10px;
    border: 0;
    border-radius: 5px;
    background: transparent;
    color: #334155;
    text-align: left;
    cursor: pointer;
}
.filter-result:hover,
.filter-result:focus {
    outline: none;
    background: #eff6ff;
    color: #1d4ed8;
}
.filter-empty {
    padding: 9px 10px;
    color: #64748b;
    font-size: .9rem;
}
.filter-actions {
    display: flex;
    gap: 8px;
    white-space: nowrap;
}
@media (max-width: 950px) {
    .matricula-filters {
        grid-template-columns: repeat(2, minmax(180px, 1fr));
    }
    .filter-actions {
        grid-column: 1 / -1;
    }
}
@media (max-width: 560px) {
    .matricula-filters {
        grid-template-columns: 1fr;
    }
    .filter-actions {
        grid-column: auto;
    }
}
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
.matricula-alumnos-cursos {
    line-height: 1.6;
    white-space: normal;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clientInput = document.getElementById('filtro_cliente');
    const clientIdInput = document.getElementById('filtro_cliente_id');
    const clientResults = document.getElementById('clientes-filtro-resultados');
    const clientFilter = document.querySelector('.client-filter-field');
    const courseInput = document.getElementById('filtro_curso');
    const courseIdInput = document.getElementById('filtro_curso_id');
    const courseResults = document.getElementById('cursos-filtro-resultados');
    const courseFilter = document.querySelector('.course-filter-field');
    const locationInput = document.getElementById('filtro_ubicacion');
    const locationIdInput = document.getElementById('filtro_ubicacion_id');
    const locationResults = document.getElementById('ubicaciones-filtro-resultados');
    const locationFilter = document.querySelector('.location-filter-field');
    const professorInput = document.getElementById('filtro_profesor');
    const professorIdInput = document.getElementById('filtro_profesor_id');
    const professorResults = document.getElementById('profesores-filtro-resultados');
    const professorFilter = document.querySelector('.professor-filter-field');
    const scheduleInput = document.getElementById('filtro_horario');
    const scheduleIdInput = document.getElementById('filtro_horario_id');
    const scheduleResults = document.getElementById('horarios-filtro-resultados');
    const scheduleFilter = document.querySelector('.schedule-filter-field');

    function configurarAutocompletado(input, hiddenInput, results, container, url, renderLabel, idField) {
        let searchTimeout;

        const closeResults = function() {
            results.classList.remove('is-open');
            results.innerHTML = '';
        };

        const buscar = function() {
            const query = input.value.trim();
            if (query.length < 2) {
                closeResults();
                return;
            }
            fetch(url + encodeURIComponent(query))
                .then(function(response) { return response.json(); })
                .then(function(items) {
                    results.innerHTML = '';
                    if (!items.length) {
                        results.innerHTML = '<div class="filter-empty">No se encontraron resultados.</div>';
                    } else {
                        items.slice(0, 20).forEach(function(item) {
                            const result = document.createElement('button');
                            result.type = 'button';
                            result.className = 'filter-result';
                            result.dataset.id = item[idField];
                            result.textContent = renderLabel(item);
                            results.appendChild(result);
                        });
                    }
                    results.classList.add('is-open');
                })
                .catch(closeResults);
        };

        input.addEventListener('input', function() {
            hiddenInput.value = '';
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(buscar, 300);
        });
        results.addEventListener('click', function(event) {
            const result = event.target.closest('.filter-result');
            if (!result) return;
            input.value = result.textContent;
            hiddenInput.value = result.dataset.id;
            closeResults();
        });
        document.addEventListener('click', function(event) {
            if (!container.contains(event.target)) closeResults();
        });
    }

    configurarAutocompletado(clientInput, clientIdInput, clientResults, clientFilter, 'index.php?view=matriculas&action=buscar_cliente&q=', function(cliente) {
        return (cliente.nombres || '') + ' ' + (cliente.apellidos || '');
    }, 'id_cliente');
    configurarAutocompletado(courseInput, courseIdInput, courseResults, courseFilter, 'index.php?view=matriculas&action=buscar_curso_filtro&q=', function(curso) {
        return curso.nombre || '';
    }, 'id_curso');
    configurarAutocompletado(locationInput, locationIdInput, locationResults, locationFilter, 'index.php?view=matriculas&action=buscar_ubicacion_filtro&q=', function(ubicacion) {
        return (ubicacion.area_nombre || '') + ' - ' + (ubicacion.descripcion || '') + ' ' + (ubicacion.numero_sub_area || '');
    }, 'id_sub_area');
    configurarAutocompletado(professorInput, professorIdInput, professorResults, professorFilter, 'index.php?view=matriculas&action=buscar_profesor_filtro&q=', function(profesor) {
        return profesor.nombre_completo || '';
    }, 'id_profesor');
    configurarAutocompletado(scheduleInput, scheduleIdInput, scheduleResults, scheduleFilter, 'index.php?view=matriculas&action=buscar_horario_filtro&q=', function(horario) {
        return horario.horario || '';
    }, 'id_curso_programado');
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
