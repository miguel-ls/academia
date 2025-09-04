<?php require_once 'views/partials/header.php'; ?>

<div class="matricula-container">
    <h1>Editar Matrícula #<?php echo htmlspecialchars($matricula['id_matricula']); ?></h1>
    <form id="form-matricula" action="index.php?view=matriculas" method="POST">
        <input type="hidden" name="action" value="actualizar_matricula">
        <input type="hidden" name="id_matricula" value="<?php echo htmlspecialchars($matricula['id_matricula']); ?>">

        <!-- SECCIÓN 1: CLIENTE -->
        <div class="section">
            <h2>1. Datos del Cliente</h2>
            <div class="form-group">
                <label for="buscar-cliente">Cliente Principal:</label>
                <input type="text" id="buscar-cliente" value="<?php echo htmlspecialchars($matricula['nombre_cliente']); ?>" disabled>
                <input type="hidden" id="id_cliente" name="id_cliente" value="<?php echo htmlspecialchars($matricula['id_cliente']); ?>" required>
                <div id="cliente-seleccionado-info" style="margin-top:10px; font-weight:bold;">Cliente seleccionado. No se puede cambiar en modo de edición.</div>
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
                            <td colspan="7" class="total-row">TOTAL:</td>
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
                <input type="hidden" id="fecha_inicio_matricula" name="fecha_inicio_matricula" value="<?php echo htmlspecialchars($matricula['fecha_inicio_clases']); ?>">
                <input type="hidden" id="fecha_fin_matricula" name="fecha_fin_matricula" value="<?php echo htmlspecialchars($matricula['fecha_fin_clases']); ?>">
                <div class="form-group">
                    <label for="id_forma_pago">Forma de Pago:</label>
                    <select id="id_forma_pago" name="id_forma_pago" required>
                        <!-- Opciones cargadas desde BD, aquí se debería seleccionar la correcta -->
                        <option value="1" <?php echo ($matricula['id_forma_pago'] == 1) ? 'selected' : ''; ?>>Efectivo</option>
                        <option value="2" <?php echo ($matricula['id_forma_pago'] == 2) ? 'selected' : ''; ?>>Tarjeta de Crédito/Débito</option>
                        <option value="3" <?php echo ($matricula['id_forma_pago'] == 3) ? 'selected' : ''; ?>>Transferencia Bancaria</option>
                        <option value="4" <?php echo ($matricula['id_forma_pago'] == 4) ? 'selected' : ''; ?>>Yape/Plin</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label for="observaciones">Observaciones:</label>
                <textarea id="observaciones" name="observaciones" rows="3"><?php echo htmlspecialchars($matricula['observaciones']); ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-success" style="width: 100%; padding: 15px; font-size: 1.2em;">Actualizar Matrícula</button>
    </form>
</div>

<!-- Embeber los datos de los detalles de la matrícula para que JS los pueda leer -->
<script>
    const matriculaDetalles = <?php echo json_encode($detalles); ?>;
</script>

<script src="<?php echo $base_url; ?>public/assets/js/matricula_form.js"></script>

<?php require_once 'views/partials/footer.php'; ?>
