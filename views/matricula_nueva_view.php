<?php require_once 'views/partials/header.php'; ?>

<div class="matricula-container">
    <h1>Nueva Matrícula</h1>
    <form id="form-matricula" action="index.php?view=matriculas" method="POST">
        <input type="hidden" name="action" value="registrar_matricula">

        <!-- ======================= SECCIÓN 1: ALUMNO ======================= -->
        <div class="section">
            <h2>1. Datos del Alumno</h2>
            <div class="form-group">
                <label for="buscar-cliente">Buscar Alumno (por nombre, apellidos o documento):</label>
                <input type="text" id="buscar-cliente" placeholder="Escriba para buscar...">
                <input type="hidden" id="id_cliente" name="id_cliente" required>
                <div id="cliente-seleccionado-info" style="margin-top:10px; font-weight:bold;"></div>
            </div>
        </div>

        <!-- ======================= SECCIÓN 2: CURSOS ======================= -->
        <div class="section">
            <h2>2. Selección de Cursos</h2>
            <fieldset>
                <legend>Filtros de Búsqueda de Cursos</legend>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="filtro-profesor">Profesor:</label>
                        <input type="text" id="filtro-profesor">
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
                        <button type="button" id="btn-buscar-cursos" class="btn">Buscar Cursos</button>
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
                            <th>Curso</th>
                            <th>Precio Orig.</th>
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
                            <td colspan="4" class="total-row">TOTAL:</td>
                            <td id="total-matricula" class="total-row">S/ 0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- ======================= SECCIÓN 3: PAGO ======================= -->
        <div class="section">
            <h2>3. Datos del Pago</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="fecha_inicio_matricula">Fecha Inicio Matrícula:</label>
                    <input type="date" id="fecha_inicio_matricula" name="fecha_inicio_matricula" readonly>
                </div>
                <div class="form-group">
                    <label for="fecha_fin_matricula">Fecha Fin Matrícula:</label>
                    <input type="date" id="fecha_fin_matricula" name="fecha_fin_matricula" readonly>
                </div>
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

        <button type="submit" class="btn btn-success" style="width: 100%; padding: 15px; font-size: 1.2em;">Registrar Matrícula</button>
    </form>
</div>

<script>
// =================================================================
// Lógica JavaScript para la página de Nueva Matrícula
// =================================================================
document.addEventListener('DOMContentLoaded', function() {

    // --- Lógica para la Sección 1: Búsqueda de Alumno ---
    const inputBuscarCliente = document.getElementById('buscar-cliente');
    const infoCliente = document.getElementById('cliente-seleccionado-info');
    const hiddenIdCliente = document.getElementById('id_cliente');

    inputBuscarCliente.addEventListener('keyup', function() {
        const query = this.value;
        if (query.length < 3) {
            // Podríamos mostrar una lista desplegable con resultados
            return;
        }
        // SIMULACIÓN DE BÚSQUEDA AJAX:
        // En la implementación real, aquí se haría un fetch a una URL como:
        // fetch(`index.php?view=matriculas&action=buscar_cliente&q=${query}`)
        // y se poblaría una lista de sugerencias.
        // Por ahora, si el usuario escribe "Juan Perez", lo seleccionamos.
        if (query.toLowerCase() === 'juan perez') {
            infoCliente.textContent = `Cliente Seleccionado: Juan Perez (Doc: 12345678)`;
            hiddenIdCliente.value = '1'; // Asumimos que Juan Perez tiene ID 1
        }
    });


    // --- Lógica para la Sección 2: Búsqueda y Selección de Cursos ---
    const btnBuscarCursos = document.getElementById('btn-buscar-cursos');
    const cursosContainer = document.getElementById('cursos-disponibles-container');
    const cursosSeleccionadosBody = document.querySelector('#cursos-seleccionados-grid tbody');

    btnBuscarCursos.addEventListener('click', function() {
        // SIMULACIÓN DE BÚSQUEDA AJAX:
        // fetch(`index.php?view=matriculas&action=buscar_cursos&...filtros`)
        // Por ahora, mostramos datos de ejemplo.
        cursosContainer.innerHTML = `
            <div class="curso-card" data-id="1" data-nombre="Curso de PHP Avanzado" data-precio="500.00">
                <h4>Curso de PHP Avanzado</h4>
                <p>Profesor: Juan Tech</p>
                <p>Precio: S/ 500.00</p>
                <button type="button" class="btn-seleccionar-curso">Seleccionar</button>
            </div>
            <div class="curso-card" data-id="2" data-nombre="Curso de MySQL" data-precio="450.00">
                <h4>Curso de MySQL</h4>
                <p>Profesor: Maria DB</p>
                <p>Precio: S/ 450.00</p>
                <button type="button" class="btn-seleccionar-curso">Seleccionar</button>
            </div>
        `;
    });

    // Usamos delegación de eventos para los botones "Seleccionar"
    cursosContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-seleccionar-curso')) {
            const card = e.target.closest('.curso-card');
            const id = card.dataset.id;
            const nombre = card.dataset.nombre;
            const precio = parseFloat(card.dataset.precio);

            // Permitir al usuario editar el precio y añadir descuento
            const precioPactado = prompt(`Precio para "${nombre}":`, precio.toFixed(2));
            const descuento = prompt(`Descuento para "${nombre}":`, "0.00");

            if (precioPactado !== null && descuento !== null) {
                agregarCursoAGrilla(id, nombre, precio, parseFloat(precioPactado), parseFloat(descuento));
            }
        }
    });

    function agregarCursoAGrilla(id, nombre, precioOrig, precioPactado, descuento) {
        const precioFinal = precioPactado - descuento;
        const newRow = document.createElement('tr');
        newRow.dataset.id = id;
        newRow.innerHTML = `
            <td>${nombre}<input type="hidden" name="cursos[${id}][id_curso]" value="${id}"></td>
            <td>${precioOrig.toFixed(2)}</td>
            <td><input type="number" name="cursos[${id}][precio_pactado]" value="${precioPactado.toFixed(2)}" readonly></td>
            <td><input type="number" name="cursos[${id}][descuento]" value="${descuento.toFixed(2)}" readonly></td>
            <td class="precio-final">${precioFinal.toFixed(2)}</td>
            <td><button type="button" class="btn-eliminar-curso">Eliminar</button></td>
        `;
        cursosSeleccionadosBody.appendChild(newRow);
        actualizarTotal();
    }

    // Delegación de eventos para eliminar curso de la grilla
    cursosSeleccionadosBody.addEventListener('click', function(e){
        if(e.target.classList.contains('btn-eliminar-curso')){
            e.target.closest('tr').remove();
            actualizarTotal();
        }
    });

    function actualizarTotal() {
        let total = 0;
        document.querySelectorAll('#cursos-seleccionados-grid .precio-final').forEach(function(item) {
            total += parseFloat(item.textContent);
        });
        document.getElementById('total-matricula').textContent = `S/ ${total.toFixed(2)}`;
    }

    // --- Lógica para la Sección 3: Fechas ---
    // Copiar fechas desde los filtros
    document.getElementById('filtro-fecha-inicio').addEventListener('change', function(){
        document.getElementById('fecha_inicio_matricula').value = this.value;
    });
    document.getElementById('filtro-fecha-fin').addEventListener('change', function(){
        document.getElementById('fecha_fin_matricula').value = this.value;
    });

});
</script>

<?php require_once 'views/partials/footer.php'; ?>
