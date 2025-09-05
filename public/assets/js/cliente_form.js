// =================================================================
// Lógica JavaScript para el formulario de Clientes (form.php)
// =================================================================
document.addEventListener('DOMContentLoaded', function() {

    // --- Referencias a elementos del Formulario ---
    const tipoDocumentoSelect = document.getElementById('id_tipo_documento');
    const labelNombres = document.getElementById('label_nombres');
    const groupApellidos = document.getElementById('group_apellidos');
    const inputDocumento = document.getElementById('numero_documento');
    const documentoError = document.getElementById('documento-error');
    const submitBtn = document.getElementById('submit-btn');
    const idClienteInput = document.getElementById('id_cliente');
    const btnSunat = document.getElementById('btn_sunat');
    const inputNombres = document.getElementById('nombres');
    const inputApellidos = document.getElementById('apellidos');
    const inputDireccion = document.getElementById('direccion');
    const inputUbigeo = document.getElementById('codigo_ubigeo');
    let debounceTimeout;

    // --- Función para manejar la lógica de RUC y visibilidad del botón Sunat ---
    function handleRucLogic() {
        if (!tipoDocumentoSelect) return;

        const selectedOptionText = tipoDocumentoSelect.options[tipoDocumentoSelect.selectedIndex].text.trim().toUpperCase();

        if (selectedOptionText === 'RUC') {
            if(labelNombres) labelNombres.textContent = 'Razón Social:';
            if(groupApellidos) groupApellidos.style.display = 'none';
            if(btnSunat) btnSunat.style.display = 'inline-block';
        } else if (selectedOptionText === 'DNI') {
            if(labelNombres) labelNombres.textContent = 'Nombres:';
            if(groupApellidos) groupApellidos.style.display = 'block';
            if(btnSunat) btnSunat.style.display = 'inline-block';
        } else {
            if(labelNombres) labelNombres.textContent = 'Nombres:';
            if(groupApellidos) groupApellidos.style.display = 'block';
            if(btnSunat) btnSunat.style.display = 'none';
        }
    }

    // --- Event Listeners ---
    if (tipoDocumentoSelect) {
        tipoDocumentoSelect.addEventListener('change', handleRucLogic);
        handleRucLogic(); // Estado inicial
    }

    if (inputDocumento) {
        inputDocumento.addEventListener('keyup', function() {
            clearTimeout(debounceTimeout);
            const numeroDocumento = this.value;
            const idCliente = idClienteInput ? idClienteInput.value : null;

            if (numeroDocumento.length < 3) return;

            debounceTimeout = setTimeout(() => {
                let url = `index.php?view=clientes&action=check_documento&numero_documento=${encodeURIComponent(numeroDocumento)}`;
                if (idCliente) url += `&id_cliente=${idCliente}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            documentoError.textContent = 'Este número de documento ya está registrado.';
                            documentoError.style.display = 'block';
                            submitBtn.disabled = true;
                        } else {
                            documentoError.style.display = 'none';
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(error => console.error('Error al verificar documento:', error));
            }, 500);
        });
    }

    if (btnSunat) {
        btnSunat.addEventListener('click', function() {
            const numero = inputDocumento.value.trim();
            const tipoDocText = tipoDocumentoSelect.options[tipoDocumentoSelect.selectedIndex].text.trim().toUpperCase();

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
                        // Si el proxy devuelve un error (4xx, 5xx), lo capturamos aquí
                        return response.json().then(errorData => {
                            throw new Error(errorData.error || 'Error en el servidor proxy.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.numeroDocumento) {
                        if (tipoDocText === 'DNI') {
                            inputNombres.value = data.nombre || ''; // Nombre completo
                            inputApellidos.value = `${data.apellidoPaterno || ''} ${data.apellidoMaterno || ''}`.trim();
                        } else if (tipoDocText === 'RUC') {
                            inputNombres.value = data.nombre || ''; // Razón Social
                            const fullAddress = `${data.direccion || ''} - ${data.departamento || ''} - ${data.provincia || ''} - ${data.distrito || ''}`.trim();
                            inputDireccion.value = fullAddress.replace(/^-| -$/g, '').replace(/ - - /g, ' - ');
                            inputUbigeo.value = data.ubigeo || '';
                        }
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
    }

    const clienteForm = document.getElementById('cliente-form');
    if(clienteForm) {
        clienteForm.addEventListener('submit', function(e) {
            const selectedOptionText = tipoDocumentoSelect.options[tipoDocumentoSelect.selectedIndex].text.trim().toUpperCase();
            const apellidosInput = document.getElementById('apellidos');

            if (selectedOptionText !== 'RUC' && apellidosInput.value.trim() === '') {
                e.preventDefault();
                documentoError.textContent = 'El campo Apellidos es obligatorio para este tipo de documento.';
                documentoError.style.display = 'block';
            }
        });
    }
});
