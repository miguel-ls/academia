<?php require_once 'views/partials/header.php'; ?>
<?php
$matricula = $matricula ?? [];
$detalles = $detalles ?? [];
$formas_pago = $formas_pago ?? [];
$error_message = $_GET['error'] ?? '';
$forma_pago_seleccionada = (string)($matricula['id_forma_pago'] ?? '');
if ($forma_pago_seleccionada === '' && !empty($matricula['forma_pago'])) {
    foreach ($formas_pago as $forma_pago) {
        if (strcasecmp(trim((string)($forma_pago['nombre'] ?? '')), trim((string)$matricula['forma_pago'])) === 0) {
            $forma_pago_seleccionada = (string)($forma_pago['id_forma_pago'] ?? '');
            break;
        }
    }
}
?>

<div class="matricula-container matricula-page">
    <h1>Editar Matrícula #<?php echo htmlspecialchars($matricula['id_matricula']); ?></h1>
    <form id="form-matricula" action="index.php?view=matriculas" method="POST">
        <input type="hidden" name="action" value="actualizar_matricula">
        <input type="hidden" name="id_matricula" value="<?php echo htmlspecialchars($matricula['id_matricula']); ?>">

        <!-- SECCIÓN 1: CLIENTE -->
        <div class="section">
            <h2>1. Datos de la Matrícula</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="buscar-cliente">Cliente Principal:</label>
                    <input type="text" id="buscar-cliente" value="<?php echo htmlspecialchars($matricula['nombre_cliente']); ?>" disabled>
                    <input type="hidden" id="id_cliente" name="id_cliente" value="<?php echo htmlspecialchars($matricula['id_cliente']); ?>" required>
                    <div id="cliente-seleccionado-info" style="margin-top:10px; font-weight:bold;">Cliente seleccionado. No se puede cambiar en modo de edición.</div>
                </div>
                <div class="form-group">
                    <label for="fecha_matricula">Fecha de Matrícula:</label>
                    <input type="date" id="fecha_matricula" name="fecha_matricula" value="<?php echo htmlspecialchars(substr((string)($matricula['fecha_matricula'] ?? ''), 0, 10), ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: CURSOS -->
        <div class="section">
            <h2>2. Selección de Cursos</h2>
            <fieldset>
                <legend>Filtros de Búsqueda de Cursos</legend>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="filtro-profesor">Profesor:</label>
                        <input type="text" id="filtro-profesor" autocomplete="off" placeholder="Escriba para buscar profesor...">
                        <input type="hidden" id="filtro-profesor-id" name="filtro_profesor_id">
                        <div id="profesor-search-results" class="search-results"></div>
                    </div>
                    <div class="form-group">
                        <label for="filtro-fecha-inicio">Desde:</label>
                        <input type="date" id="filtro-fecha-inicio">
                    </div>
                    <div class="form-group">
                        <label for="filtro-fecha-fin">Hasta:</label>
                        <input type="date" id="filtro-fecha-fin">
                    </div>
                    <div class="form-group" style="align-self: flex-end;">
                        <button type="button" id="btn-buscar-cursos" class="btn btn-primary">Buscar Cursos</button>
                    </div>
                </div>
            </fieldset>

            <div id="cursos-disponibles-container">
                <p>Los cursos disponibles aparecerán aquí...</p>
            </div>

            <h3>Cursos Agregados a la Matrícula</h3>
            <div id="cursos-seleccionados-grid">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cliente Asistente</th>
                            <th>Curso</th>
                            <th>Ubicación</th>
                            <th>Profesor</th>
                            <th>Horario y Horas</th>
                            <th>Fecha Inicial de Clases</th>
                            <th>Fecha Final de Clases</th>
                            <th>Precio Pactado</th>
                            <th>Descuento</th>
                            <th>Precio Final</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Las filas de cursos se añadirán aquí con JS -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9" class="total-row">TOTAL:</td>
                            <td id="total-matricula" class="total-row">S/ 0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- SECCIÓN 3: PAGO -->
        <div class="section">
            <h2>3. Datos del Pago</h2>
            <div class="form-grid">
                <!-- Las fechas se copian desde los filtros y se envían de forma oculta -->
                <div class="form-group">
                    <label for="id_forma_pago">Forma de Pago:</label>
                    <select id="id_forma_pago" name="id_forma_pago">
                        <option value="">Seleccione una forma de pago</option>
                        <?php foreach ($formas_pago as $forma_pago): ?>
                            <?php $id_forma_pago = (string)($forma_pago['id_forma_pago'] ?? ''); ?>
                            <option value="<?php echo htmlspecialchars($id_forma_pago, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $forma_pago_seleccionada === $id_forma_pago ? 'selected' : ''; ?>><?php echo htmlspecialchars((string)($forma_pago['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label for="observaciones">Observaciones:</label>
                <textarea id="observaciones" name="observaciones" rows="3"><?php echo htmlspecialchars((string)($matricula['observaciones'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?view=matriculas" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-success">Actualizar Matrícula</button>
        </div>
    </form>
</div>

<style>
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 20px;
}
.form-actions .btn {
    padding: 10px 20px;
    font-size: 1em;
}
</style>

<?php require_once 'views/partials/modal_error.php'; ?>

<!-- Embeber los datos de los detalles de la matrícula para que JS los pueda leer -->
<script>
    const matriculaDetalles = <?php echo json_encode($detalles ?? []); ?>;
</script>

<script src="<?php echo $base_url; ?>public/assets/js/matricula_form.js?v=<?php echo time(); ?>"></script>

<?php require_once 'views/partials/footer.php'; ?>
