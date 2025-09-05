// =================================================================
// Lógica JavaScript para la página de Nueva Matrícula (v7 - Final Fix)
// =================================================================
document.addEventListener('DOMContentLoaded', function() {

    // --- Referencias a elementos del Modal ---
    const modal = document.getElementById('modal-nuevo-cliente');
    const btnNuevoCliente = document.getElementById('btn-nuevo-cliente');
    const btnCerrarModal = document.getElementById('modal-close-btn');
    const btnCancelarModal = document.getElementById('modal-cancel-btn');
    const formNuevoCliente = document.getElementById('form-nuevo-cliente');
    const modalErrorMessage = document.getElementById('modal-error-message');
    const modalInputDocumento = document.getElementById('modal_numero_documento');
    const modalDocumentoError = document.getElementById('modal-documento-error');
    const modalSubmitBtn = formNuevoCliente.querySelector('button[type="submit"]');
    const modalTipoDocumento = document.getElementById('modal_id_tipo_documento');
    const modalLabelNombres = document.getElementById('label_modal_nombres');
    const modalGroupApellidos = document.getElementById('group_modal_apellidos');
    const modalInputApellidos = document.getElementById('modal_apellidos');
    const modalBtnSunat = document.getElementById('btn_sunat_modal');
    const modalInputNombres = document.getElementById('modal_nombres');
    const modalInputDireccion = document.getElementById('modal_direccion');
    const modalInputUbigeo = document.getElementById('modal_codigo_ubigeo');
    let debounceTimeout;


    // --- Lógica para la Sección 1: Búsqueda de Cliente Principal ---
    const inputBuscarCliente = document.getElementById('buscar-cliente');
    const resultsContainer = document.getElementById('cliente-search-results');
    const infoCliente = document.getElementById('cliente-seleccionado-info');
    const hiddenIdCliente = document.getElementById('id_cliente');
    let mainClientSearchTimeout;

    inputBuscarCliente.addEventListener('keyup', function() {
        clearTimeout(mainClientSearchTimeout);
        const query = this.value;
        btnNuevoCliente.style.display = 'none';
        if (query.length < 2) { resultsContainer.innerHTML = ''; return; }
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
                                btnNuevoCliente.style.display = 'none';
                            });
                            list.appendChild(item);
                        });
                        resultsContainer.appendChild(list);
                    } else {
                        resultsContainer.innerHTML = '<div class="search-results-list"><div class="search-results-item">No se encontraron clientes.</div></div>';
                        btnNuevoCliente.style.display = 'block';
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

    // --- Lógica del Modal de Nuevo Cliente ---
    btnNuevoCliente.addEventListener('click', function() {
        modal.style.display = 'flex';
    });

    function cerrarModal() {
        modal.style.display = 'none';
        formNuevoCliente.reset();
        modalErrorMessage.style.display = 'none';
        modalDocumentoError.style.display = 'none';
        modalSubmitBtn.disabled = false;
        modalLabelNombres.textContent = 'Nombres:';
        modalGroupApellidos.style.display = 'block';
        modalBtnSunat.style.display = 'none';
    }

    btnCerrarModal.addEventListener('click', cerrarModal);
    btnCancelarModal.addEventListener('click', cerrarModal);

    modalTipoDocumento.addEventListener('change', function() {
        const selectedOptionText = this.options[this.selectedIndex].text.trim().toUpperCase();
        if (selectedOptionText === 'RUC') {
            modalLabelNombres.textContent = 'Razón Social:';
            modalGroupApellidos.style.display = 'none';
            modalBtnSunat.style.display = 'inline-block';
        } else if (selectedOptionText === 'DNI') {
            modalLabelNombres.textContent = 'Nombres:';
            modalGroupApellidos.style.display = 'block';
            modalBtnSunat.style.display = 'inline-block';
        } else {
            modalLabelNombres.textContent = 'Nombres:';
            modalGroupApellidos.style.display = 'block';
            modalBtnSunat.style.display = 'none';
        }
    });

    modalBtnSunat.addEventListener('click', function() {
        const numero = modalInputDocumento.value.trim();
        const tipoDocText = modalTipoDocumento.options[modalTipoDocumento.selectedIndex].text.trim().toUpperCase();

        if ((tipoDocText === 'DNI' && numero.length !== 8) || (tipoDocText === 'RUC' && numero.length !== 11)) {
            alert(`El número de ${tipoDocText} debe tener ${tipoDocText === 'DNI' ? 8 : 11} dígitos.`);
            return;
        }

        const apiUrl = `index.php?view=clientes&action=proxy_sunat&tipo=${tipoDocText}&numero=${numero}`;

        this.textContent = '...';
        this.disabled = true;

        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.error || 'Error en el servidor proxy.');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.numeroDocumento) {
                    const fullAddress = `${data.direccion || ''} - ${data.departamento || ''} - ${data.provincia || ''} - ${data.distrito || ''}`.trim();

                    if (tipoDocText === 'DNI') {
                        modalInputNombres.value = data.nombre || ''; // Nombre completo
                        modalInputApellidos.value = `${data.apellidoPaterno || ''} ${data.apellidoMaterno || ''}`.trim();
                    } else if (tipoDocText === 'RUC') {
                        modalInputNombres.value = data.nombre || ''; // Razón Social
                    }

                    // Llenar dirección y ubigeo para ambos casos
                    modalInputDireccion.value = fullAddress.replace(/^-| -$/g, '').replace(/ - - /g, ' - ');
                    modalInputUbigeo.value = data.ubigeo || '';
                } else {
                    alert('No se encontraron datos para el documento ingresado.');
                }
            })
            .catch(error => {
                console.error('Error en la consulta a la API:', error);
                alert('Ocurrió un error al consultar el servicio.');
            })
            .finally(() => {
                this.textContent = 'Sunat';
                this.disabled = false;
            });
    });

    modalInputDocumento.addEventListener('keyup', function() {
        clearTimeout(debounceTimeout);
        const numeroDocumento = this.value;
        if (numeroDocumento.length < 3) { return; }
        debounceTimeout = setTimeout(() => {
            fetch(`index.php?view=clientes&action=check_documento&numero_documento=${encodeURIComponent(numeroDocumento)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        modalDocumentoError.textContent = 'Este número de documento ya está registrado.';
                        modalDocumentoError.style.display = 'block';
                        modalSubmitBtn.disabled = true;
                    } else {
                        modalDocumentoError.style.display = 'none';
                        modalSubmitBtn.disabled = false;
                    }
                })
                .catch(error => console.error('Error al verificar documento:', error));
        }, 500);
    });

    formNuevoCliente.addEventListener('submit', function(e) {
        e.preventDefault();
        modalErrorMessage.style.display = 'none';
        if (modalSubmitBtn.disabled) {
            modalErrorMessage.textContent = 'Por favor, corrija los errores antes de continuar.';
            modalErrorMessage.style.display = 'block';
            return;
        }
        const selectedOptionText = modalTipoDocumento.options[modalTipoDocumento.selectedIndex].text.trim().toUpperCase();
        if (selectedOptionText !== 'RUC' && modalInputApellidos.value.trim() === '') {
            modalErrorMessage.textContent = 'El campo Apellidos es obligatorio para este tipo de documento.';
            modalErrorMessage.style.display = 'block';
            return;
        }
        const formData = new FormData(this);
        if (selectedOptionText === 'RUC') {
            formData.set('apellidos', '');
        }
        fetch('index.php?view=clientes&action=crear_ajax', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) { throw new Error(`Error del servidor: ${response.status}`); }
            return response.json();
        })
        .then(data => {
            if (data.success && data.cliente) {
                const cliente = data.cliente;
                hiddenIdCliente.value = cliente.id_cliente;
                const nombreCompleto = (cliente.apellidos) ? `${cliente.nombres} ${cliente.apellidos}` : cliente.nombres;
                infoCliente.textContent = `Cliente Seleccionado: ${nombreCompleto}`;
                inputBuscarCliente.value = nombreCompleto;
                resultsContainer.innerHTML = '';
                btnNuevoCliente.style.display = 'none';
                cerrarModal();
            } else {
                modalErrorMessage.textContent = data.error || 'Ocurrió un error desconocido.';
                modalErrorMessage.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error en la creación del cliente:', error);
            modalErrorMessage.textContent = 'Error de conexión. Inténtelo de nuevo.';
            modalErrorMessage.style.display = 'block';
        });
    });

    // --- Lógica de Búsqueda de Cursos y Grilla (sin cambios) ---
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
                        card.dataset.dias_semana_raw = curso.dias_semana;
                        card.dataset.fecha_inicio_raw = curso.fecha_inicio;
                        card.dataset.fecha_fin_raw = curso.fecha_fin;
                        card.dataset.hora_inicio_raw = curso.hora_inicio;
                        card.dataset.hora_fin_raw = curso.hora_fin;
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
            .catch(error => console.error('Error al buscar cursos:', error));
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
                clienteNombre: mainClientName,
                dias_semana_raw: card.dataset.dias_semana_raw,
                fecha_inicio_raw: card.dataset.fecha_inicio_raw,
                fecha_fin_raw: card.dataset.fecha_fin_raw,
                hora_inicio_raw: card.dataset.hora_inicio_raw,
                hora_fin_raw: card.dataset.hora_fin_raw
            });
        }
    });
    function agregarCursoAGrilla(curso) {
        const precioPactado = curso.precio_pactado !== undefined ? curso.precio_pactado : curso.precio;
        const descuento = curso.descuento !== undefined ? curso.descuento : 0.00;
        const precioFinal = precioPactado - descuento;
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
            <td>
                ${curso.nombre}
                <input type="hidden" name="cursos[${curso.id}][dias_semana]" value="${curso.dias_semana_raw}">
                <input type="hidden" name="cursos[${curso.id}][fecha_inicio]" value="${curso.fecha_inicio_raw}">
                <input type="hidden" name="cursos[${curso.id}][fecha_fin]" value="${curso.fecha_fin_raw}">
                <input type="hidden" name="cursos[${curso.id}][hora_inicio]" value="${curso.hora_inicio_raw}">
                <input type="hidden" name="cursos[${curso.id}][hora_fin]" value="${curso.hora_fin_raw}">
            </td>
            <td>${curso.ubicacion}</td>
            <td>${curso.profesor}</td>
            <td>${curso.horario}<br><small>${curso.horas}</small></td>
            <td><input type="number" class="recalc-trigger" name="cursos[${curso.id}][precio_pactado]" value="${parseFloat(precioPactado).toFixed(2)}" step="0.01"></td>
            <td><input type="number" class="recalc-trigger" name="cursos[${curso.id}][descuento]" value="${parseFloat(descuento).toFixed(2)}" step="0.01"></td>
            <td class="precio-final">${precioFinal.toFixed(2)}</td>
            <td><button type="button" class="btn btn-danger btn-eliminar-curso">Eliminar</button></td>
        `;
        cursosSeleccionadosBody.appendChild(newRow);
        actualizarTotal();
    }
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
    document.getElementById('filtro-fecha-inicio').addEventListener('change', function(){
        document.getElementById('fecha_inicio_matricula').value = this.value;
    });
    document.getElementById('filtro-fecha-fin').addEventListener('change', function(){
        document.getElementById('fecha_fin_matricula').value = this.value;
    });
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
    const formMatricula = document.getElementById('form-matricula');
    formMatricula.addEventListener('submit', function(e) {
        if (!validarCruceHorariosCliente()) {
            e.preventDefault();
            return;
        }
    });
    function validarCruceHorariosCliente() {
        const cursosPorCliente = {};
        const filas = document.querySelectorAll('#cursos-seleccionados-grid tbody tr');
        if (filas.length === 0) {
            alert('Debe agregar al menos un curso a la matrícula.');
            return false;
        }
        for (const fila of filas) {
            const clienteId = fila.querySelector('.id-cliente-asistente').value;
            const clienteNombre = fila.querySelector('.cliente-asistente-search').value;
            if (!cursosPorCliente[clienteId]) {
                cursosPorCliente[clienteId] = {
                    nombre: clienteNombre,
                    cursos: []
                };
            }
            const cursoInfo = {
                nombreCurso: fila.cells[1].innerText.trim(),
                ubicacion: fila.cells[2].innerText.trim(),
                dias: fila.querySelector('input[name*="[dias_semana]"]').value.split(','),
                horaInicio: fila.querySelector('input[name*="[hora_inicio]"]').value,
                horaFin: fila.querySelector('input[name*="[hora_fin]"]').value
            };
            cursosPorCliente[clienteId].cursos.push(cursoInfo);
        }
        for (const clienteId in cursosPorCliente) {
            const dataCliente = cursosPorCliente[clienteId];
            const cursos = dataCliente.cursos;
            const nombreCliente = dataCliente.nombre;
            if (cursos.length > 1) {
                for (let i = 0; i < cursos.length; i++) {
                    for (let j = i + 1; j < cursos.length; j++) {
                        const curso1 = cursos[i];
                        const curso2 = cursos[j];
                        const diasEnComun = curso1.dias.some(dia => curso2.dias.includes(dia));
                        const horasSeCruzan = (curso1.horaInicio < curso2.horaFin) && (curso1.horaFin > curso2.horaInicio);
                        const mismaUbicacion = curso1.ubicacion === curso2.ubicacion;
                        if (diasEnComun && horasSeCruzan && mismaUbicacion) {
                            alert(`Error de validación:\nEl cliente "${nombreCliente}" tiene un cruce de horario.\n\n- Curso 1: ${curso1.nombreCurso}\n- Curso 2: ${curso2.nombreCurso}\n- Ubicación: ${curso1.ubicacion}\n\nPor favor, corrija la selección antes de continuar.`);
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
    function inicializarGrillaParaEdicion() {
        if (typeof matriculaDetalles !== 'undefined' && matriculaDetalles.length > 0) {
            const formatTime = (timeString) => {
                if (!timeString) return '';
                let [hours, minutes] = timeString.split(':');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                return `${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
            };
            matriculaDetalles.forEach(detalle => {
                agregarCursoAGrilla({
                    id: detalle.id_curso_programado,
                    nombre: detalle.nombre_curso,
                    precio_pactado: detalle.precio_pactado,
                    descuento: detalle.descuento,
                    ubicacion: detalle.ubicacion,
                    profesor: detalle.profesor,
                    horario: detalle.horario_dias,
                    horas: `${formatTime(detalle.hora_inicio)} - ${formatTime(detalle.hora_fin)}`,
                    clienteId: detalle.id_cliente_asistencia,
                    clienteNombre: detalle.nombre_cliente_asistencia,
                    dias_semana_raw: detalle.dias_semana,
                    fecha_inicio_raw: detalle.fecha_inicio,
                    fecha_fin_raw: detalle.fecha_fin,
                    hora_inicio_raw: detalle.hora_inicio,
                    hora_fin_raw: detalle.hora_fin
                });
            });
        }
    }
    inicializarGrillaParaEdicion();
});
