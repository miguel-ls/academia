// =================================================================
// Lógica JavaScript para la página de Nueva Matrícula
// =================================================================
document.addEventListener('DOMContentLoaded', function() {

    // --- Lógica para la Sección 1: Búsqueda de Cliente Principal ---
    const inputBuscarCliente = document.getElementById('buscar-cliente');
    const resultsContainer = document.getElementById('cliente-search-results');
    const infoCliente = document.getElementById('cliente-seleccionado-info');
    const hiddenIdCliente = document.getElementById('id_cliente');
    let mainClientSearchTimeout;

    inputBuscarCliente.addEventListener('keyup', function() {
        clearTimeout(mainClientSearchTimeout);
        const query = this.value;

        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            return;
        }

        mainClientSearchTimeout = setTimeout(() => {
            fetch(`index.php?view=matriculas&action=buscar_cliente&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
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

                            item.addEventListener('click', function() {
                                hiddenIdCliente.value = this.dataset.id;
                                infoCliente.textContent = `Cliente Seleccionado: ${this.dataset.nombre}`;
                                inputBuscarCliente.value = this.dataset.nombre;
                                resultsContainer.innerHTML = '';
                            });
                            list.appendChild(item);
                        });
                        resultsContainer.appendChild(list);
                    } else {
                        resultsContainer.innerHTML = '<div class="search-results-list"><div class="search-results-item">No se encontraron clientes.</div></div>';
                    }
                })
                .catch(error => console.error('Error en la búsqueda de clientes:', error));
        }, 300);
    });

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
        const profesorId = document.getElementById('filtro-profesor-id').value;
        const fechaInicio = document.getElementById('filtro-fecha-inicio').value;
        const fechaFin = document.getElementById('filtro-fecha-fin').value;

        let url = `index.php?view=matriculas&action=buscar_cursos`;
        if (profesorId) url += `&profesor_id=${profesorId}`;
        if (fechaInicio) url += `&fecha_inicio=${fechaInicio}`;
        if (fechaFin) url += `&fecha_fin=${fechaFin}`;

        cursosContainer.innerHTML = '<p>Buscando cursos...</p>';

        const formatDate = (dateString) => {
            if (!dateString) return '';
            const date = new Date(dateString + 'T00:00:00');
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        };

        const formatTime = (timeString) => {
            if (!timeString) return '';
            let [hours, minutes] = timeString.split(':');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            return `${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
        };

        fetch(url)
            .then(response => response.json())
            .then(data => {
                cursosContainer.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(curso => {
                        const card = document.createElement('div');
                        card.className = 'curso-card';
                        card.dataset.id = curso.id_curso_programado;
                        card.dataset.nombre = curso.nombre_curso;
                        card.dataset.precio = curso.precio_actual || '0.00';
                        card.dataset.ubicacion = `${curso.area} - ${curso.sub_area} ${curso.numero_sub_area}`;
                        card.dataset.profesor = curso.nombre_profesor;
                        card.dataset.horario = curso.horario_dias;
                        card.dataset.horas = `${formatTime(curso.hora_inicio)} - ${formatTime(curso.hora_fin)}`;

                        card.innerHTML = `
                            <h4>${curso.nombre_curso}</h4>
                            <p><small><strong>Ubicación:</strong> ${card.dataset.ubicacion}</small></p>
                            <p><strong>Profesor:</strong> ${card.dataset.profesor}</p>
                            <p><strong>Periodo:</strong> ${formatDate(curso.fecha_inicio)} - ${formatDate(curso.fecha_fin)}</p>
                            <p><strong>Horario:</strong> ${card.dataset.horario}</p>
                            <p><strong>Horas:</strong> ${card.dataset.horas}</p>
                            <p><strong>Precio:</strong> S/ ${parseFloat(curso.precio_actual || 0).toFixed(2)}</p>
                            <p><strong>Vacantes:</strong> ${curso.vacantes_disponibles}</p>
                            <button type="button" class="btn btn-primary btn-seleccionar-curso">Seleccionar</button>
                        `;
                        cursosContainer.appendChild(card);
                    });
                } else {
                    cursosContainer.innerHTML = '<p>No se encontraron cursos con los filtros seleccionados.</p>';
                }
            })
            .catch(error => {
                console.error('Error al buscar cursos:', error);
                cursosContainer.innerHTML = '<p>Ocurrió un error al buscar los cursos.</p>';
            });
    });

    cursosContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-seleccionar-curso')) {
            const card = e.target.closest('.curso-card');
            if (!hiddenIdCliente.value) {
                alert('Por favor, seleccione un cliente principal primero.');
                return;
            }
            const mainClientName = inputBuscarCliente.value;
            const mainClientId = hiddenIdCliente.value;

            agregarCursoAGrilla({
                id: card.dataset.id,
                nombre: card.dataset.nombre,
                precio: parseFloat(card.dataset.precio),
                ubicacion: card.dataset.ubicacion,
                profesor: card.dataset.profesor,
                horario: card.dataset.horario,
                horas: card.dataset.horas,
                clienteId: mainClientId,
                clienteNombre: mainClientName
            });
        }
    });

    function agregarCursoAGrilla(curso) {
        const precioFinal = curso.precio; // Descuento es 0 al inicio
        const newRow = document.createElement('tr');
        newRow.dataset.id = curso.id;

        const uniqueId = `cliente_asistente_${curso.id}`;

        newRow.innerHTML = `
            <td>
                <div class="search-results">
                    <input type="text" id="${uniqueId}" class="cliente-asistente-search" value="${curso.clienteNombre}" placeholder="Buscar cliente...">
                    <input type="hidden" class="id-cliente-asistente" name="cursos[${curso.id}][id_cliente_asistencia]" value="${curso.clienteId}">
                    <div class="search-results-list-inline"></div>
                </div>
            </td>
            <td>${curso.nombre}<input type="hidden" name="cursos[${curso.id}][id_curso]" value="${curso.id}"></td>
            <td>${curso.ubicacion}</td>
            <td>${curso.profesor}</td>
            <td>${curso.horario}</td>
            <td>${curso.horas}</td>
            <td>${curso.precio.toFixed(2)}</td>
            <td><input type="number" class="recalc-trigger" name="cursos[${curso.id}][precio_pactado]" value="${curso.precio.toFixed(2)}" step="0.01"></td>
            <td><input type="number" class="recalc-trigger" name="cursos[${curso.id}][descuento]" value="0.00" step="0.01"></td>
            <td class="precio-final">${precioFinal.toFixed(2)}</td>
            <td><button type="button" class="btn btn-danger btn-eliminar-curso">Eliminar</button></td>
        `;
        cursosSeleccionadosBody.appendChild(newRow);
        actualizarTotal();
    }

    // --- Lógica para la Búsqueda de Cliente en la Grilla ---
    let gridClientSearchTimeout;
    cursosSeleccionadosBody.addEventListener('keyup', function(e) {
        if (e.target.classList.contains('cliente-asistente-search')) {
            const input = e.target;
            const parentCell = input.closest('td');
            const resultsList = parentCell.querySelector('.search-results-list-inline');
            const hiddenInput = parentCell.querySelector('.id-cliente-asistente');

            clearTimeout(gridClientSearchTimeout);
            const query = input.value;

            if (query.length < 2) {
                resultsList.innerHTML = '';
                return;
            }

            gridClientSearchTimeout = setTimeout(() => {
                fetch(`index.php?view=matriculas&action=buscar_cliente&q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        resultsList.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(cliente => {
                                const item = document.createElement('div');
                                item.className = 'search-results-item';
                                item.textContent = `${cliente.nombres} ${cliente.apellidos}`;
                                item.dataset.id = cliente.id_cliente;
                                item.addEventListener('click', function() {
                                    input.value = this.textContent;
                                    hiddenInput.value = this.dataset.id;
                                    resultsList.innerHTML = '';
                                });
                                resultsList.appendChild(item);
                            });
                        } else {
                           resultsList.innerHTML = '<div class="search-results-item">No encontrado</div>';
                        }
                    });
            }, 300);
        }
    });

    cursosSeleccionadosBody.addEventListener('click', function(e){
        if(e.target.classList.contains('btn-eliminar-curso')){
            e.target.closest('tr').remove();
            actualizarTotal();
        }
    });

    cursosSeleccionadosBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('recalc-trigger')) {
            const row = e.target.closest('tr');
            const precioPactadoInput = row.querySelector('input[name*="[precio_pactado]"]');
            const descuentoInput = row.querySelector('input[name*="[descuento]"]');
            const precioFinalCell = row.querySelector('.precio-final');

            const precioPactado = parseFloat(precioPactadoInput.value) || 0;
            const descuento = parseFloat(descuentoInput.value) || 0;
            const precioFinal = precioPactado - descuento;

            precioFinalCell.textContent = precioFinal.toFixed(2);
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
    document.getElementById('filtro-fecha-inicio').addEventListener('change', function(){
        document.getElementById('fecha_inicio_matricula').value = this.value;
    });
    document.getElementById('filtro-fecha-fin').addEventListener('change', function(){
        document.getElementById('fecha_fin_matricula').value = this.value;
    });


    // --- Lógica para la Sección 2: Búsqueda de Profesor ---
    const inputBuscarProfesor = document.getElementById('filtro-profesor');
    const hiddenProfesorId = document.getElementById('filtro-profesor-id');
    const profesorResultsContainer = document.getElementById('profesor-search-results');
    let profesorSearchTimeout;

    inputBuscarProfesor.addEventListener('keyup', function() {
        clearTimeout(profesorSearchTimeout);
        hiddenProfesorId.value = '';
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
                            item.dataset.id = profesor.id_profesor;

                            item.addEventListener('click', function() {
                                inputBuscarProfesor.value = this.textContent;
                                hiddenProfesorId.value = this.dataset.id;
                                profesorResultsContainer.innerHTML = '';
                            });
                            list.appendChild(item);
                        });
                        profesorResultsContainer.appendChild(list);
                    } else {
                        profesorResultsContainer.innerHTML = '<div class="search-results-list"><div class="search-results-item">No se encontraron profesores.</div></div>';
                    }
                })
                .catch(error => console.error('Error en la búsqueda de profesores:', error));
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (profesorResultsContainer && !profesorResultsContainer.contains(e.target) && e.target !== inputBuscarProfesor) {
            profesorResultsContainer.innerHTML = '';
        }
    });

});
