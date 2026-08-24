<?php
$clientes_filtro = $clientes_filtro ?? [];
$filtro_cliente_id = (int)($filtro_cliente_id ?? 0);
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
            <input type="text" id="filtro_cliente" class="form-control" autocomplete="off" placeholder="Buscar cliente..." aria-autocomplete="list" aria-controls="clientes-filtro-resultados" value="<?php
                foreach ($clientes_filtro as $cliente) {
                    if ((int)$cliente['id_cliente'] === $filtro_cliente_id) {
                        echo htmlspecialchars(trim($cliente['nombres'] . ' ' . $cliente['apellidos']), ENT_QUOTES, 'UTF-8');
                        break;
                    }
                }
            ?>">
            <input type="hidden" id="filtro_cliente_id" name="cliente_id" value="<?php echo $filtro_cliente_id ?: ''; ?>">
            <div id="clientes-filtro-resultados" class="filter-results" role="listbox"></div>
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
                    <td><?php echo htmlspecialchars($matricula['nombre_cliente']); ?></td>
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
    grid-template-columns: minmax(230px, 1.5fr) repeat(3, minmax(150px, 1fr)) auto;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clientInput = document.getElementById('filtro_cliente');
    const clientIdInput = document.getElementById('filtro_cliente_id');
    const clientResults = document.getElementById('clientes-filtro-resultados');
    const clientFilter = document.querySelector('.client-filter-field');
    const clients = <?php echo json_encode(array_map(function ($cliente) {
        return [
            'id' => (int)$cliente['id_cliente'],
            'nombre' => trim($cliente['nombres'] . ' ' . $cliente['apellidos'])
        ];
    }, $clientes_filtro), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    function closeClientResults() {
        clientResults.classList.remove('is-open');
        clientResults.innerHTML = '';
    }

    function renderClientResults(query) {
        const normalizedQuery = query.trim().toLocaleLowerCase('es');
        const matches = clients.filter(client => client.nombre.toLocaleLowerCase('es').includes(normalizedQuery)).slice(0, 30);
        clientResults.innerHTML = '';
        if (matches.length === 0) {
            clientResults.innerHTML = '<div class="filter-empty">No se encontraron clientes.</div>';
        } else {
            matches.forEach(client => {
                const result = document.createElement('button');
                result.type = 'button';
                result.className = 'filter-result';
                result.dataset.id = client.id;
                result.textContent = client.nombre;
                clientResults.appendChild(result);
            });
        }
        clientResults.classList.add('is-open');
    }

    clientInput.addEventListener('input', function() {
        clientIdInput.value = '';
        renderClientResults(this.value);
    });
    clientInput.addEventListener('focus', function() {
        renderClientResults(this.value);
    });
    clientResults.addEventListener('click', function(event) {
        const result = event.target.closest('.filter-result');
        if (!result) return;
        clientInput.value = result.textContent;
        clientIdInput.value = result.dataset.id;
        closeClientResults();
    });
    document.addEventListener('click', function(event) {
        if (!clientFilter.contains(event.target)) closeClientResults();
    });
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
