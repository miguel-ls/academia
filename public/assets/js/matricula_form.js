// =================================================================
// Lógica JavaScript para la página de Nueva Matrícula
// =================================================================
document.addEventListener('DOMContentLoaded', function() {

    // --- Lógica para la Sección 1: Búsqueda de Cliente ---
    const inputBuscarCliente = document.getElementById('buscar-cliente');
    const resultsContainer = document.getElementById('cliente-search-results');
    const infoCliente = document.getElementById('cliente-seleccionado-info');
    const hiddenIdCliente = document.getElementById('id_cliente');
    let searchTimeout;

    inputBuscarCliente.addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        const query = this.value;

        if (query.length < 2) { // Reducido a 2 para mejor usabilidad
            resultsContainer.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`index.php?view=matriculas&action=buscar_cliente&q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    resultsContainer.innerHTML = '';
                    if (data.length > 0) {
                        const list = document.createElement('div');
                        list.className = 'search-results-list';
                        data.forEach(cliente => {
                            const item = document.createElement('div');
                            item.className = 'search-results-item';
                            item.textContent = `${cliente.nombres} ${cliente.apellidos} (${cliente.tipo_documento}: ${cliente.numero_documento})`;
                            item.dataset.id = cliente.id_cliente;
                            item.dataset.nombre = `${cliente.nombres} ${cliente.apellidos}`;
                            item.dataset.documento = `${cliente.tipo_documento}: ${cliente.numero_documento}`;

                            item.addEventListener('click', function() {
                                hiddenIdCliente.value = this.dataset.id;
                                infoCliente.textContent = `Cliente Seleccionado: ${this.dataset.nombre} (${this.dataset.documento})`;
                                inputBuscarCliente.value = '';
                                resultsContainer.innerHTML = '';
                            });
                            list.appendChild(item);
                        });
                        resultsContainer.appendChild(list);
                    } else {
                        resultsContainer.innerHTML = '<div class="search-results-list"><div class="search-results-item">No se encontraron clientes.</div></div>';
                    }
                })
                .catch(error => {
                    console.error('Error en la búsqueda de clientes:', error);
                    resultsContainer.innerHTML = '<div class="search-results-list"><div class="search-results-item">Error al buscar.</div></div>';
                });
        }, 300); // Debounce para no saturar con peticiones
    });

    // Ocultar resultados si se hace clic fuera
    document.addEventListener('click', function(e) {
        if (resultsContainer && !resultsContainer.contains(e.target) && e.target !== inputBuscarCliente) {
            resultsContainer.innerHTML = '';
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
                <button type="button" class="btn btn-primary btn-seleccionar-curso">Seleccionar</button>
            </div>
            <div class="curso-card" data-id="2" data-nombre="Curso de MySQL" data-precio="450.00">
                <h4>Curso de MySQL</h4>
                <p>Profesor: Maria DB</p>
                <p>Precio: S/ 450.00</p>
                <button type="button" class="btn btn-primary btn-seleccionar-curso">Seleccionar</button>
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
            <td><button type="button" class="btn btn-danger btn-eliminar-curso">Eliminar</button></td>
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


    // --- Lógica para la Sección 2: Búsqueda de Profesor ---
    const inputBuscarProfesor = document.getElementById('filtro-profesor');
    const profesorResultsContainer = document.getElementById('profesor-search-results');
    let profesorSearchTimeout;

    inputBuscarProfesor.addEventListener('keyup', function() {
        clearTimeout(profesorSearchTimeout);
        const query = this.value;

        if (query.length < 2) {
            profesorResultsContainer.innerHTML = '';
            return;
        }

        profesorSearchTimeout = setTimeout(() => {
            fetch(`index.php?view=profesores&action=buscar&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    profesorResultsContainer.innerHTML = '';
                    if (data.length > 0) {
                        const list = document.createElement('div');
                        list.className = 'search-results-list';
                        data.forEach(profesor => {
                            const item = document.createElement('div');
                            item.className = 'search-results-item';
                            item.textContent = profesor.nombre_completo;

                            item.addEventListener('click', function() {
                                inputBuscarProfesor.value = this.textContent;
                                profesorResultsContainer.innerHTML = '';
                            });
                            list.appendChild(item);
                        });
                        profesorResultsContainer.appendChild(list);
                    } else {
                        profesorResultsContainer.innerHTML = '<div class="search-results-list"><div class="search-results-item">No se encontraron profesores.</div></div>';
                    }
                })
                .catch(error => {
                    console.error('Error en la búsqueda de profesores:', error);
                    profesorResultsContainer.innerHTML = '<div class="search-results-list"><div class="search-results-item">Error al buscar.</div></div>';
                });
        }, 300);
    });

    // Ocultar resultados de profesor si se hace clic fuera
    document.addEventListener('click', function(e) {
        if (profesorResultsContainer && !profesorResultsContainer.contains(e.target) && e.target !== inputBuscarProfesor) {
            profesorResultsContainer.innerHTML = '';
        }
    });

});
