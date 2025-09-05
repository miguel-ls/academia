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
    const idClienteInput = document.getElementById('id_cliente'); // Existe solo en modo edición
    let debounceTimeout;

    // --- Función para manejar la lógica de RUC ---
    function handleRucLogic() {
        if (!tipoDocumentoSelect) return;

        const selectedOptionText = tipoDocumentoSelect.options[tipoDocumentoSelect.selectedIndex].text.trim().toUpperCase();

        if (selectedOptionText === 'RUC') {
            if(labelNombres) labelNombres.textContent = 'Razón Social:';
            if(groupApellidos) groupApellidos.style.display = 'none';
        } else {
            if(labelNombres) labelNombres.textContent = 'Nombres:';
            if(groupApellidos) groupApellidos.style.display = 'block';
        }
    }

    // --- Event Listener para el cambio de tipo de documento ---
    if (tipoDocumentoSelect) {
        tipoDocumentoSelect.addEventListener('change', handleRucLogic);
        // Llamar una vez al cargar la página para establecer el estado inicial (importante para modo edición)
        handleRucLogic();
    }

    // --- Event Listener para la validación de documento duplicado ---
    // --- Lógica de validación en el envío del formulario ---
    const clienteForm = document.getElementById('cliente-form');
    if(clienteForm) {
        clienteForm.addEventListener('submit', function(e) {
            // Validar apellidos solo si es requerido
            const selectedOptionText = tipoDocumentoSelect.options[tipoDocumentoSelect.selectedIndex].text.trim().toUpperCase();
            const apellidosInput = document.getElementById('apellidos');

            if (selectedOptionText !== 'RUC' && apellidosInput.value.trim() === '') {
                e.preventDefault(); // Detener el envío
                // Re-usar el div de error de documento para este mensaje o crear uno nuevo
                documentoError.textContent = 'El campo Apellidos es obligatorio para este tipo de documento.';
                documentoError.style.display = 'block';
            }
        });
    }

    if (inputDocumento) {
        inputDocumento.addEventListener('keyup', function() {
            clearTimeout(debounceTimeout);
            const numeroDocumento = this.value;
            const idCliente = idClienteInput ? idClienteInput.value : null;

            if (numeroDocumento.length < 3) {
                return;
            }

            debounceTimeout = setTimeout(() => {
                let url = `index.php?view=clientes&action=check_documento&numero_documento=${encodeURIComponent(numeroDocumento)}`;
                if (idCliente) {
                    url += `&id_cliente=${idCliente}`;
                }

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

});
