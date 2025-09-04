<?php require_once 'views/partials/header.php'; ?>

<div class="matricula-container">
    <h1>Nueva Matrícula</h1>
    <form id="form-matricula" action="index.php?view=matriculas" method="POST">
        <input type="hidden" name="action" value="registrar_matricula">

        <!-- SECCIÓN 1: CLIENTE -->
        <div class="section">
            <h2>1. Datos del Cliente</h2>
            <div class="form-group">
                <label for="buscar-cliente">Buscar Cliente (por nombre, apellidos o documento):</label>
                <input type="text" id="buscar-cliente" placeholder="Escriba para buscar..." autocomplete="off">
                <div id="cliente-search-results" class="search-results"></div>
                <input type="hidden" id="id_cliente" name="id_cliente" required>
                <div id="cliente-seleccionado-info" style="margin-top:10px; font-weight:bold;"></div>
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
                <input type="hidden" id="fecha_inicio_matricula" name="fecha_inicio_matricula">
                <input type="hidden" id="fecha_fin_matricula" name="fecha_fin_matricula">
                <div class="form-group">
                    <label for="id_forma_pago">Forma de Pago:</label>
                    <select id="id_forma_pago" name="id_forma_pago" required>
                        <!-- Opciones cargadas desde BD -->
                        <option value="1">Efectivo</option>
                        <option value="2">Tarjeta de Crédito/Débito</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label for="observaciones">Observaciones:</label>
                <textarea id="observaciones" name="observaciones" rows="3"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?view=matriculas" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-success">Registrar Matrícula</button>
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

<script>
    // Definir la variable global para que el script no falle en esta página
    const matriculaDetalles = [];
</script>

<script src="<?php echo $base_url; ?>public/assets/js/matricula_form.js"></script>

<?php require_once 'views/partials/footer.php'; ?>
