<?php
$detalle_matricula = $detalle_matricula ?? [];
$clases = $clases ?? [];
$id = (int)($id ?? 0);
$page = (int)($page ?? 1);
$total_pages = (int)($total_pages ?? 0);
$feedback_message = $feedback_message ?? null;
require_once 'views/partials/header.php';
?>

<div class="page-header">
    <h1>Marcar Asistencia de Cliente</h1>
    <div>
        <a href="index.php?view=asistencia_clientes" class="btn btn-secondary">&laquo; Volver a la lista</a>
        <button type="button" id="btn-agregar-dias" class="btn btn-primary">Agregar días</button>
    </div>
</div>

<!-- Enrollment Details Section -->
<div class="card">
    <h2>Detalles de la Matrícula</h2>
    <p><strong>Curso:</strong> <?php echo htmlspecialchars($detalle_matricula['curso_nombre']); ?></p>
    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($detalle_matricula['cliente_nombre']); ?></p>
    <p><strong>Profesor:</strong> <?php echo htmlspecialchars($detalle_matricula['profesor_nombre']); ?></p>
    <p><strong>Periodo:</strong> <?php echo date('d/m/Y', strtotime($detalle_matricula['fecha_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($detalle_matricula['fecha_fin'])); ?></p>
    <p><strong>Horario:</strong> <?php echo htmlspecialchars($detalle_matricula['tipo_horario_nombre']); ?> (<?php echo date('h:i A', strtotime($detalle_matricula['hora_inicio'])); ?> - <?php echo date('h:i A', strtotime($detalle_matricula['hora_fin'])); ?>)</p>
    <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($detalle_matricula['ubicacion']); ?></p>
</div>

<div class="page-header-right">
    <button type="button" id="btn-eliminar-masivo" class="btn btn-danger">Eliminar seleccionados</button>
    <button type="button" id="btn-cambiar-estado-masivo" class="btn btn-secondary">Cambiar estado</button>
    <button type="button" id="btn-modificar-asistencia" class="btn btn-warning">Modificar Asistencias</button>
</div>
<!-- Attendance Form -->
<form action="index.php?view=asistencia_clientes&action=guardar" method="POST">
    <input type="hidden" name="id_matricula_detalle" value="<?php echo $id; ?>">

    <table class="table">
        <thead>
            <tr>
                <th><input type="checkbox" id="chk-select-all" title="Seleccionar todos los programados"></th>
                <th>Fecha de Clase</th>
                <th>Estado</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clases)): ?>
                <tr>
                    <td colspan="5">No hay clases generadas para esta matrícula.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($clases as $clase): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="chk-dia" value="<?php echo (int)$clase['id_asistencia_cliente']; ?>">
                        </td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($clase['fecha_clase'])) . ' (' . htmlspecialchars($clase['dia_semana_es']) . ')'; ?>
                        </td>
                        <td class="cell-estado">
                            <span class="badge status-<?php echo strtolower(htmlspecialchars($clase['estado'])); ?> view-mode">
                                <?php echo htmlspecialchars($clase['estado']); ?>
                            </span>
                            <select name="asistencias[<?php echo $clase['id_asistencia_cliente']; ?>][estado]" required class="form-control attendance-select estado-select estado-<?php echo strtolower(str_replace('ó', 'o', $clase['estado'])); ?> edit-mode" style="display:none;">
                                <option value="Programado" <?php echo ($clase['estado'] == 'Programado') ? 'selected' : ''; ?>>Programado</option>
                                <option value="Asistió" <?php echo ($clase['estado'] == 'Asistió') ? 'selected' : ''; ?>>Asistió</option>
                                <option value="Faltó" <?php echo ($clase['estado'] == 'Faltó') ? 'selected' : ''; ?>>Faltó</option>
                                <option value="Justificado" <?php echo ($clase['estado'] == 'Justificado') ? 'selected' : ''; ?>>Justificado</option>
                                <option value="Postergado" <?php echo ($clase['estado'] == 'Postergado') ? 'selected' : ''; ?>>Postergado</option>
                                <option value="Cancelado" <?php echo ($clase['estado'] == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                        </td>
                        <td class="cell-observaciones">
                            <span class="view-mode"><?php echo htmlspecialchars($clase['observaciones']); ?></span>
                            <input type="text" name="asistencias[<?php echo $clase['id_asistencia_cliente']; ?>][observaciones]" value="<?php echo htmlspecialchars($clase['observaciones']); ?>" class="form-control attendance-input edit-mode" style="display:none;">
                        </td>
                        <td class="cell-actions">
                            <?php if ($clase['estado'] !== 'Asistió'): ?>
                                <button type="button" class="btn btn-danger btn-eliminar-dia" data-id="<?php echo (int)$clase['id_asistencia_cliente']; ?>">Eliminar</button>
                            <?php else: ?>
                                <span class="action-disabled">No disponible</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginación -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination-container">
            <?php if ($page > 1): ?>
                <a href="index.php?view=asistencia_clientes&action=marcar&id=<?php echo $id; ?>&page=<?php echo $page - 1; ?>" class="btn btn-primary">&laquo; Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="index.php?view=asistencia_clientes&action=marcar&id=<?php echo $id; ?>&page=<?php echo $i; ?>" class="btn <?php echo ($i == $page) ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="index.php?view=asistencia_clientes&action=marcar&id=<?php echo $id; ?>&page=<?php echo $page + 1; ?>" class="btn btn-primary">Siguiente &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="form-actions edit-mode" style="display:none;">
        <a href="index.php?view=asistencia_clientes&action=marcar&id=<?php echo $id; ?>" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Grabar Asistencia</button>
    </div>
</form>

<form id="form-eliminar-masivo" action="index.php?view=asistencia_clientes&action=eliminar_dias_masivo&id=<?php echo $id; ?>" method="POST" style="display:none;"></form>

<div id="modal-agregar-dias" class="attendance-modal" aria-hidden="true">
    <div class="attendance-modal-content" role="dialog" aria-modal="true" aria-labelledby="modal-agregar-dias-title">
        <button type="button" class="attendance-modal-close" aria-label="Cerrar">&times;</button>
        <h2 id="modal-agregar-dias-title">Agregar días de clase</h2>
        <p>Seleccione la fecha final. Solo se agregarán los días correspondientes al horario del curso que aún no existan.</p>
        <form action="index.php?view=asistencia_clientes&action=agregar_dias&id=<?php echo $id; ?>" method="POST">
            <label for="fecha_fin_nuevas">Fecha final</label>
            <input type="date" id="fecha_fin_nuevas" name="fecha_fin_nuevas" required min="<?php echo date('Y-m-d'); ?>">
            <div class="attendance-modal-actions">
                <button type="button" class="btn btn-secondary attendance-modal-cancel">Cancelar</button>
                <button type="submit" class="btn btn-primary">Generar</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-cambiar-estado" class="attendance-modal" aria-hidden="true">
    <div class="attendance-modal-content" role="dialog" aria-modal="true" aria-labelledby="modal-cambiar-estado-title">
        <button type="button" class="attendance-modal-close" aria-label="Cerrar">&times;</button>
        <h2 id="modal-cambiar-estado-title">Cambiar estado</h2>
        <p>Se actualizará el estado de todos los días seleccionados en la grilla.</p>
        <form id="form-cambiar-estado" action="index.php?view=asistencia_clientes&action=cambiar_estado_masivo&id=<?php echo $id; ?>" method="POST">
            <label for="nuevo_estado">Nuevo estado</label>
            <select id="nuevo_estado" name="nuevo_estado" required>
                <option value="Programado">Programado</option>
                <option value="Asistió">Asistió</option>
                <option value="Faltó">Faltó</option>
                <option value="Justificado">Justificado</option>
                <option value="Postergado">Postergado</option>
            </select>
            <div class="attendance-modal-actions">
                <button type="button" class="btn btn-secondary attendance-modal-cancel">Cancelar</button>
                <button type="submit" class="btn btn-primary">Grabar</button>
            </div>
        </form>
    </div>
</div>

<?php if ($feedback_message): ?>
<div id="feedback-modal" class="attendance-modal is-open" aria-hidden="false">
    <div class="attendance-modal-content" role="dialog" aria-modal="true" aria-labelledby="feedback-title">
        <button type="button" class="attendance-modal-close" aria-label="Cerrar">&times;</button>
        <h2 id="feedback-title">Mensaje</h2>
        <p><?php echo htmlspecialchars($feedback_message); ?></p>
        <div class="attendance-modal-actions"><button type="button" class="btn btn-primary attendance-modal-close-button">Aceptar</button></div>
    </div>
</div>
<?php endif; ?>

<style>
.badge {
    padding: 5px 10px;
    border-radius: 12px;
    color: #fff;
    font-weight: bold;
    font-size: 0.9em;
    text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
}
.status-programado { background-color: #17a2b8; } /* Info */
.status-asistió { background-color: #28a745; } /* Success */
.status-faltó { background-color: #dc3545; } /* Danger */
.status-justificado { background-color: #ffc107; color: #212529; } /* Warning */
.status-postergado { background-color: #6c757d; } /* Secondary */
.status-cancelado { background-color: #343a40; } /* Dark */
.attendance-select,
.attendance-input {
    width: 100%;
    min-height: 40px;
    padding: 9px 11px;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    background: #fff;
    color: #243b53;
    font: inherit;
    box-sizing: border-box;
    transition: border-color .2s ease, box-shadow .2s ease;
}
.attendance-select:focus,
.attendance-input:focus {
    outline: 0;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .14);
}
.estado-select {
    font-weight: 600;
    border-width: 2px !important;
}
.estado-select.estado-programado { border-color: #17a2b8 !important; color: #0f6674; }
.estado-select.estado-asistio { border-color: #28a745 !important; color: #1e7e34; }
.estado-select.estado-falto { border-color: #dc3545 !important; color: #a71d2a; }
.estado-select.estado-justificado { border-color: #ffc107 !important; color: #8a6d00; }
.estado-select.estado-postergado { border-color: #6c757d !important; color: #495057; }
.estado-select.estado-cancelado { border-color: #343a40 !important; color: #343a40; }
.attendance-input::placeholder {
    color: #94a3b8;
}
.page-header { align-items: center; }
.attendance-modal {
    position: fixed; inset: 0; z-index: 2000; display: none; align-items: center; justify-content: center;
    padding: 20px; background: rgba(15, 23, 42, .55);
}
.attendance-modal.is-open { display: flex; }
.attendance-modal-content {
    position: relative; width: min(100%, 480px); padding: 24px; border-radius: 12px;
    background: #fff; box-shadow: 0 18px 45px rgba(15, 23, 42, .25);
}
.attendance-modal-content h2 { margin: 0 0 10px; color: #243b53; }
.attendance-modal-content p { margin: 0 0 18px; color: #52677a; }
.attendance-modal-content label { display: block; margin-bottom: 6px; font-weight: 600; }
.attendance-modal-content input[type="date"],
.attendance-modal-content select { width: 100%; min-height: 40px; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 7px; font: inherit; box-sizing: border-box; }
.attendance-modal-close { position: absolute; top: 10px; right: 14px; border: 0; background: transparent; color: #64748b; font-size: 25px; cursor: pointer; }
.attendance-modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
.cell-actions { white-space: nowrap; }
.form-eliminar-dia { margin: 0; }
.action-disabled { color: #94a3b8; font-size: .8rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnModificar = document.getElementById('btn-modificar-asistencia');
    const viewModeElements = document.querySelectorAll('.view-mode');
    const editModeElements = document.querySelectorAll('.edit-mode');
    const addDaysModal = document.getElementById('modal-agregar-dias');
    const openAddDays = document.getElementById('btn-agregar-dias');
    const closeModals = document.querySelectorAll('.attendance-modal-close, .attendance-modal-cancel, .attendance-modal-close-button');
    const chkSelectAll = document.getElementById('chk-select-all');
    const chkDias = document.querySelectorAll('.chk-dia');
    const btnEliminarMasivo = document.getElementById('btn-eliminar-masivo');
    const formEliminarMasivo = document.getElementById('form-eliminar-masivo');
    const btnCambiarEstadoMasivo = document.getElementById('btn-cambiar-estado-masivo');
    const cambiarEstadoModal = document.getElementById('modal-cambiar-estado');
    const formCambiarEstado = document.getElementById('form-cambiar-estado');

    if (chkSelectAll) {
        chkSelectAll.addEventListener('change', function() {
            chkDias.forEach(function(chk) { chk.checked = chkSelectAll.checked; });
        });
    }

    btnEliminarMasivo.addEventListener('click', function() {
        const seleccionados = Array.from(chkDias).filter(function(chk) { return chk.checked; });
        if (seleccionados.length === 0) {
            alert('Seleccione al menos un día para eliminar.');
            return;
        }
        if (!confirm(`¿Eliminar ${seleccionados.length} día(s) seleccionado(s)?`)) return;
        formEliminarMasivo.innerHTML = '';
        seleccionados.forEach(function(chk) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids_asistencia_cliente[]';
            input.value = chk.value;
            formEliminarMasivo.appendChild(input);
        });
        formEliminarMasivo.submit();
    });

    btnCambiarEstadoMasivo.addEventListener('click', function() {
        const seleccionados = Array.from(chkDias).filter(function(chk) { return chk.checked; });
        if (seleccionados.length === 0) {
            alert('Seleccione al menos un día para cambiar de estado.');
            return;
        }
        cambiarEstadoModal.classList.add('is-open');
    });

    formCambiarEstado.addEventListener('submit', function(e) {
        const seleccionados = Array.from(chkDias).filter(function(chk) { return chk.checked; });
        if (seleccionados.length === 0) {
            e.preventDefault();
            alert('Seleccione al menos un día para cambiar de estado.');
            return;
        }
        formCambiarEstado.querySelectorAll('input[name="ids_asistencia_cliente[]"]').forEach(function(input) { input.remove(); });
        seleccionados.forEach(function(chk) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids_asistencia_cliente[]';
            input.value = chk.value;
            formCambiarEstado.appendChild(input);
        });
    });

    openAddDays.addEventListener('click', function() { addDaysModal.classList.add('is-open'); });
    closeModals.forEach(function(button) {
        button.addEventListener('click', function() { this.closest('.attendance-modal').classList.remove('is-open'); });
    });
    document.querySelectorAll('.btn-eliminar-dia').forEach(function(button) {
        button.addEventListener('click', function() {
            if (!confirm('¿Eliminar este día programado?')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `index.php?view=asistencia_clientes&action=eliminar_dia&id=<?php echo $id; ?>`;
            form.innerHTML = `<input type="hidden" name="id_asistencia_cliente" value="${this.dataset.id}">`;
            document.body.appendChild(form);
            form.submit();
        });
    });

    btnModificar.addEventListener('click', function() {
        viewModeElements.forEach(el => el.style.display = 'none');
        editModeElements.forEach(el => el.style.display = ''); // Revert to default display
        this.style.display = 'none'; // Hide the modify button itself
    });

    document.querySelectorAll('.estado-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const base = Array.from(this.classList).filter(function(c) { return !c.startsWith('estado-') || c === 'estado-select'; });
            this.className = base.join(' ') + ' estado-' + this.value.toLowerCase().replace('ó', 'o');
        });
    });
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
