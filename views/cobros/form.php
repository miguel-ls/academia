<?php
$matriculas_pendientes = $matriculas_pendientes ?? [];
$formas_pago = $formas_pago ?? [];
$error_message = $error_message ?? '';
$selected_matricula = $selected_matricula ?? null;
$saldo_pendiente = (float)($saldo_pendiente ?? 0.00);
$is_edit = !empty($cobro_actual) || (($_GET['action'] ?? '') === 'edit' || ($_GET['action'] ?? '') === 'ver');
$readonly = (($_GET['action'] ?? '') === 'ver');
$action_name = $readonly ? 'ver' : ($is_edit ? 'update' : 'create');

if (!empty($cobro_actual)) {
    $selected_matricula = [
        'id_matricula' => $cobro_actual['id_matricula'],
        'nombre_cliente' => $cobro_actual['nombre_cliente'],
        'alumnos_cursos' => $cobro_actual['alumnos_cursos'] ?? '',
        'monto_final' => $cobro_actual['monto_final'],
        'saldo_pendiente' => $cobro_actual['saldo_pendiente'],
    ];
    $saldo_pendiente = (float)($cobro_actual['saldo_pendiente'] ?? 0.00);
}

require_once 'views/partials/header.php';
?>

<div class="cobros-page">
    <div class="page-header">
        <div class="page-header-left">
            <h1><?php echo $readonly ? 'Ver Cobro' : ($is_edit ? 'Editar Cobro' : 'Nuevo Cobro'); ?></h1>
        </div>
        <!-- <div class="page-header-right">
            <a href="index.php?view=cobros" class="btn btn-secondary">Volver</a>
        </div> -->
    </div>

<form method="POST" action="index.php?view=cobros" class="cobros-form-panel">
    <input type="hidden" name="action" value="<?php echo htmlspecialchars($action_name); ?>">
    <?php if ($is_edit && !empty($cobro_actual)): ?>
        <input type="hidden" name="id_cobro" value="<?php echo (int)$cobro_actual['id_cobro']; ?>">
    <?php endif; ?>
    <input type="hidden" name="id_matricula" id="id_matricula" value="<?php echo (int)($selected_matricula['id_matricula'] ?? $cobro_actual['id_matricula'] ?? 0); ?>">

    <div class="panel">
        <div class="panel-header-row">
            <h3>Matrícula asociada</h3>
            <?php if (!$readonly): ?>
                <button type="button" class="btn btn-success" id="btn-buscar-matricula">Buscar matrícula</button>
            <?php endif; ?>
        </div>

        <div id="matricula-seleccionada" class="matricula-summary" <?php echo (!empty($selected_matricula)) ? '' : 'style="display:none;"'; ?>>
            <div class="summary-grid">
                <div>
                    <strong>Matrícula:</strong> <span id="selected-id-matricula"><?php echo (int)($selected_matricula['id_matricula'] ?? 0); ?></span>
                </div>
                <div>
                    <strong>Cliente:</strong> <span id="selected-nombre-cliente"><?php echo htmlspecialchars($selected_matricula['nombre_cliente'] ?? ''); ?></span>
                </div>
                <div class="summary-detail-full">
                    <strong>Detalle de la matrícula:</strong>
                    <span id="selected-alumnos-cursos"><?php echo nl2br(htmlspecialchars($selected_matricula['alumnos_cursos'] ?? '', ENT_QUOTES, 'UTF-8')); ?></span>
                </div>
                <div>
                    <strong>Importe total:</strong> S/ <span id="selected-monto-final"><?php echo number_format((float)($selected_matricula['monto_final'] ?? 0), 2); ?></span>
                </div>
                <div>
                    <strong>Saldo pendiente:</strong> S/ <span id="selected-saldo-pendiente"><?php echo number_format((float)($selected_matricula['saldo_pendiente'] ?? $saldo_pendiente), 2); ?></span>
                </div>
                </div>
        </div>
    </div>

    <div class="panel form-grid">
        <div class="form-field">
            <label for="id_forma_pago">Forma de pago</label>
            <select name="id_forma_pago" id="id_forma_pago" class="form-control" <?php echo $readonly ? 'disabled' : ''; ?>>
                <option value="">Seleccione</option>
                <?php foreach ($formas_pago as $forma): ?>
                    <option value="<?php echo (int)$forma['id_forma_pago']; ?>" <?php echo ((int)($cobro_actual['id_forma_pago'] ?? 0) === (int)$forma['id_forma_pago'] || ((int)($_POST['id_forma_pago'] ?? 0) === (int)$forma['id_forma_pago'])) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($forma['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-field">
            <label for="fecha_cobro">Fecha de cobro</label>
            <input type="date" name="fecha_cobro" id="fecha_cobro" class="form-control" value="<?php echo htmlspecialchars($cobro_actual['fecha_cobro'] ?? ($_POST['fecha_cobro'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8'); ?>" <?php echo $readonly ? 'disabled' : ''; ?>>
        </div>

        <div class="form-field">
            <label for="numero_operacion">Número de operación</label>
            <input type="text" name="numero_operacion" id="numero_operacion" class="form-control" maxlength="20" value="<?php echo htmlspecialchars($cobro_actual['numero_operacion'] ?? ($_POST['numero_operacion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" <?php echo $readonly ? 'disabled' : ''; ?>>
        </div>

        <div class="form-field">
            <label for="importe">Importe</label>
            <input type="number" step="0.01" min="0.01" name="importe" id="importe" class="form-control" value="<?php echo htmlspecialchars($cobro_actual['importe'] ?? ($_POST['importe'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" <?php echo $readonly ? 'disabled' : ''; ?>>
        </div>

        <div class="form-field form-field-full">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" id="observaciones" class="form-control" rows="3" <?php echo $readonly ? 'disabled' : ''; ?>><?php echo htmlspecialchars($cobro_actual['observaciones'] ?? ($_POST['observaciones'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
    </div>

    <?php if (!$readonly): ?>
        <div class="form-actions">
            <button type="submit" class="btn btn-success">Grabar</button>
            <a href="index.php?view=cobros" class="btn btn-secondary">Cancelar</a>
        </div>
    <?php else: ?>
        <div class="form-actions">
            <a href="index.php?view=cobros" class="btn btn-secondary">Volver</a>
        </div>
    <?php endif; ?>
</form>

<div id="matricula-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content modal-large modal-matricula-content">
        <div class="modal-header">
            <h2>Seleccionar matrícula pendiente</h2>
            <span class="modal-close" data-close="matricula-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="modal-search-row">
                <input type="text" id="matricula-search" class="form-control" placeholder="Buscar por cliente, matrícula o documento">
                <button type="button" id="matricula-search-btn" class="btn btn-primary">Buscar</button>
            </div>
            <div class="table-wrap">
                <table class="table table-sm" id="matriculas-pendientes-table">
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Saldo pendiente</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($matriculas_pendientes)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No hay matrículas pendientes de cobro.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($matriculas_pendientes as $matricula): ?>
                                <tr>
                                    <td><?php echo (int)$matricula['id_matricula']; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($matricula['nombres_clientes'] ?? '', ENT_QUOTES, 'UTF-8')); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($matricula['fecha_matricula'])); ?></td>
                                    <td>S/ <?php echo number_format((float)($matricula['saldo_pendiente'] ?? 0), 2); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-success select-matricula" data-id="<?php echo (int)$matricula['id_matricula']; ?>" data-cliente="<?php echo htmlspecialchars($matricula['nombres_clientes'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-resumen="<?php echo htmlspecialchars($matricula['alumnos_cursos'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-total="<?php echo number_format((float)($matricula['monto_final'] ?? 0), 2, '.', ''); ?>" data-saldo="<?php echo number_format((float)($matricula['saldo_pendiente'] ?? 0), 2, '.', ''); ?>">Seleccionar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($error_message)): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        window.showMessageModal && window.showMessageModal(<?php echo json_encode($error_message, JSON_UNESCAPED_UNICODE); ?>, { title: 'Error' });
    });
    </script>
<?php endif; ?>

<?php require_once 'views/partials/modal_error.php'; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('matricula-modal');
    const searchInput = document.getElementById('matricula-search');
    const searchBtn = document.getElementById('matricula-search-btn');
    const tableBody = document.querySelector('#matriculas-pendientes-table tbody');
    const buttonOpen = document.getElementById('btn-buscar-matricula');
    const saldoInput = document.getElementById('id_matricula');
    const matriculaSelected = document.getElementById('matricula-seleccionada');

    const openModal = function () {
        modal.style.display = 'flex';
        fetchPendingMatriculas(searchInput.value.trim());
    };

    const closeModal = function () {
        modal.style.display = 'none';
    };

    if (buttonOpen) {
        buttonOpen.addEventListener('click', openModal);
    }
    document.querySelectorAll('[data-close="matricula-modal"]').forEach(function (btn) {
        btn.addEventListener('click', closeModal);
    });
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    const seleccionarMatricula = function (button) {
        const id = button.dataset.id;
        const cliente = button.dataset.cliente;
        const resumen = button.dataset.resumen || '';
        const total = Number(button.dataset.total || 0).toFixed(2);
        const saldo = Number(button.dataset.saldo || 0).toFixed(2);

        saldoInput.value = id;
        matriculaSelected.style.display = 'block';
        document.getElementById('selected-id-matricula').textContent = id;
        document.getElementById('selected-nombre-cliente').textContent = cliente;
        document.getElementById('selected-alumnos-cursos').textContent = resumen;
        document.getElementById('selected-monto-final').textContent = total;
        document.getElementById('selected-saldo-pendiente').textContent = saldo;
        closeModal();
    };

    const renderRows = function (items) {
        if (!items || !items.length) {
            tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No hay matrículas pendientes de cobro.</td></tr>';
            return;
        }
        tableBody.innerHTML = items.map(function (item) {
            const saldo = Number(item.saldo_pendiente ?? 0).toFixed(2);
            const total = Number(item.monto_final ?? 0).toFixed(2);
            const nombre = (item.nombres_clientes || '').replace(/"/g, '&quot;');
            const resumen = (item.alumnos_cursos || '').replace(/"/g, '&quot;');
            return '<tr>' +
                '<td>' + (item.id_matricula || '') + '</td>' +
                '<td>' + nombre + '</td>' +
                '<td>' + (item.fecha_matricula ? new Date(item.fecha_matricula).toLocaleDateString('es-PE') : '') + '</td>' +
                '<td>S/ ' + saldo + '</td>' +
                '<td><button type="button" class="btn btn-success select-matricula" data-id="' + item.id_matricula + '" data-cliente="' + nombre + '" data-resumen="' + resumen + '" data-total="' + total + '" data-saldo="' + saldo + '">Seleccionar</button></td>' +
                '</tr>';
        }).join('');
    };

    const fetchPendingMatriculas = function (query) {
        fetch('index.php?view=cobros&action=buscar_pendientes&q=' + encodeURIComponent(query || ''))
            .then(function (response) { return response.json(); })
            .then(function (data) {
                renderRows(data || []);
            })
            .catch(function () {
                window.showMessageModal('No se pudieron cargar las matrículas pendientes.', { title: 'Error' });
            });
    };

    if (searchBtn) {
        searchBtn.addEventListener('click', function () {
            fetchPendingMatriculas(searchInput.value.trim());
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                fetchPendingMatriculas(searchInput.value.trim());
            }
        });
    }

    tableBody.addEventListener('click', function (event) {
        const button = event.target.closest('.select-matricula');
        if (button) {
            seleccionarMatricula(button);
        }
    });
});
</script>

<style>
.form-container { display: flex; flex-direction: column; gap: 20px; }
.panel { background: #fff; border: 1px solid #dbe4ec; border-radius: 10px; padding: 18px; box-shadow: 0 3px 12px rgba(15, 31, 48, .04); }
.panel-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; }
.summary-detail-full { grid-column: 1 / -1; line-height: 1.6; white-space: pre-line; }
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
.form-field { display: flex; flex-direction: column; gap: 6px; }
.form-field-full { grid-column: 1 / -1; }
.form-actions { display: flex; gap: 10px; justify-content: flex-end; }
.modal-large { width: min(980px, 92vw); }
.modal-matricula-content {
    width: min(1120px, 92vw);
    max-width: 1120px;
    max-height: 80vh;
    overflow: hidden;
}
.modal-matricula-content .modal-header,
.modal-matricula-content .modal-body {
    padding: 1.1rem 1.2rem;
}
.modal-matricula-content .modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #e6edf5;
    background: #f8fbff;
}
.modal-matricula-content .modal-header h2 {
    margin: 0;
    color: #1f2d3d;
    font-size: 1.25rem;
}
.modal-matricula-content .modal-close {
    font-size: 1.8rem;
    line-height: 1;
    cursor: pointer;
    color: #64748b;
}
.modal-matricula-content .modal-body {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: calc(80vh - 72px);
}
.table-wrap { max-height: 430px; overflow: auto; border: 1px solid #e2e8f0; border-radius: 10px; }
.modal-search-row { display: flex; gap: 10px; margin-bottom: 0; align-items: center; }
.modal-search-row .form-control {
    flex: 1;
    min-height: 42px;
    border: 1px solid #cbd6e2;
    border-radius: 8px;
    padding: 0.7rem 0.8rem;
    color: #243b53;
    background: #fff;
}
.modal-search-row .form-control:focus {
    border-color: #2185d5;
    box-shadow: 0 0 0 3px rgba(33, 133, 213, 0.12);
    outline: none;
}
.modal-search-row .btn {
    min-width: 110px;
    min-height: 42px;
}
#matriculas-pendientes-table {
    margin: 0;
    border-collapse: collapse;
    width: 100%;
}
#matriculas-pendientes-table th {
    background: #f7f9fc;
    color: #58708b;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.8rem 0.9rem;
    border-bottom: 1px solid #e2e8f0;
}
#matriculas-pendientes-table td {
    padding: 0.85rem 0.9rem;
    border-bottom: 1px solid #edf1f5;
    color: #485c72;
    font-size: 0.88rem;
    vertical-align: middle;
}
#matriculas-pendientes-table tbody tr:nth-child(even) { background: #fbfcfe; }
#matriculas-pendientes-table tbody tr:hover { background: #eef7ff; }
#matriculas-pendientes-table .btn {
    padding: 0.42rem 0.7rem;
    border-radius: 6px;
    font-size: 0.76rem;
}
@media (max-width: 640px) {
    .modal-matricula-content {
        width: 94vw;
        max-width: 94vw;
    }
    .modal-search-row { align-items: stretch; flex-direction: column; }
    .modal-search-row .btn { width: 100%; }
}
</style>
