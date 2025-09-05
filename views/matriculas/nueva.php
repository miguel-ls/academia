<?php require_once 'views/partials/header.php'; ?>

<!-- Estilos para el Modal -->
<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-container {
    background: white;
    padding: 20px;
    border-radius: 5px;
    width: 90%;
    max-width: 600px;
    position: relative;
}
.modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
}
.validation-error-modal {
    color: red;
    font-size: 0.9em;
    margin-top: 5px;
}
</style>

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
                <button type="button" id="btn-nuevo-cliente" class="btn btn-success" style="display: none; margin-top: 10px;">Nuevo Cliente</button>
            </div>
        </div>

        <!-- SECCIÓN 2: CURSOS (sin cambios) -->
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
            <div id="cursos-disponibles-container"><p>Los cursos disponibles aparecerán aquí...</p></div>
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
                    <tbody></tbody>
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

        <!-- SECCIÓN 3: PAGO (sin cambios) -->
        <div class="section">
            <h2>3. Datos del Pago</h2>
            <div class="form-grid">
                <input type="hidden" id="fecha_inicio_matricula" name="fecha_inicio_matricula">
                <input type="hidden" id="fecha_fin_matricula" name="fecha_fin_matricula">
                <div class="form-group">
                    <label for="id_forma_pago">Forma de Pago:</label>
                    <select id="id_forma_pago" name="id_forma_pago" required>
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

<!-- Modal para Nuevo Cliente (Campos Reordenados) -->
<div id="modal-nuevo-cliente" class="modal-overlay">
    <div class="modal-container">
        <span id="modal-close-btn" class="modal-close">&times;</span>
        <h2>Crear Nuevo Cliente</h2>
        <div id="modal-error-message" class="info-message error-message" style="display: none;"></div>
        <form id="form-nuevo-cliente">
            <div class="form-row">
                <div class="form-group">
                    <label for="modal_id_tipo_documento">Tipo de Documento:</label>
                    <select id="modal_id_tipo_documento" name="id_tipo_documento" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($tipos_documento as $tipo): ?>
                            <option value="<?php echo $tipo['id_tipo_documento']; ?>"><?php echo htmlspecialchars($tipo['descripcion']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal_numero_documento">Número de Documento:</label>
                    <input type="text" id="modal_numero_documento" name="numero_documento" required>
                    <div id="modal-documento-error" class="validation-error-modal" style="display: none;"></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="modal_nombres" id="label_modal_nombres">Nombres:</label>
                    <input type="text" id="modal_nombres" name="nombres" required>
                </div>
                <div class="form-group" id="group_modal_apellidos">
                    <label for="modal_apellidos">Apellidos:</label>
                    <input type="text" id="modal_apellidos" name="apellidos"> <!-- `required` eliminado -->
                </div>
            </div>
            <div class="form-row">
                 <div class="form-group">
                    <label for="modal_email">Email:</label>
                    <input type="email" id="modal_email" name="email">
                </div>
                <div class="form-group">
                    <label for="modal_telefono">Teléfono:</label>
                    <input type="text" id="modal_telefono" name="telefono">
                </div>
            </div>
            <div class="form-group">
                <label for="modal_codigo_erp">Código ERP:</label>
                <input type="text" id="modal_codigo_erp" name="codigo_erp">
            </div>
        <div class="form-row">
            <div class="form-group">
                <label for="modal_direccion">Dirección:</label>
                <input type="text" id="modal_direccion" name="direccion">
            </div>
            <div class="form-group">
                <label for="modal_codigo_ubigeo">Código de Ubigeo:</label>
                <input type="text" id="modal_codigo_ubigeo" name="codigo_ubigeo">
            </div>
        </div>
            <div class="form-actions">
                <button type="button" id="modal-cancel-btn" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Crear Cliente</button>
            </div>
        </form>
    </div>
</div>

<script>
    const matriculaDetalles = [];
</script>
<script src="<?php echo $base_url; ?>public/assets/js/matricula_form.js"></script>

<?php require_once 'views/partials/footer.php'; ?>
