// Archivo JavaScript principal.
// Puede ser usado para añadir interactividad global al sitio.
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema de Academia cargado.');

    // --- Validación de Número de Documento Duplicado para Clientes ---
    const clienteForm = document.getElementById('cliente-form');

    if (clienteForm) {
        const numeroDocumentoInput = document.getElementById('numero_documento');
        const idClienteInput = document.getElementById('id_cliente');
        const errorDiv = document.getElementById('documento-error');
        const submitBtn = document.getElementById('submit-btn');
        let isDocumentoDuplicado = false;
        const originalDocumento = numeroDocumentoInput.value;

        const checkDocumento = async () => {
            const numeroDocumento = numeroDocumentoInput.value.trim();
            const idCliente = idClienteInput ? idClienteInput.value : null;

            if (!numeroDocumento || (idCliente && numeroDocumento === originalDocumento)) {
                errorDiv.style.display = 'none';
                submitBtn.disabled = false;
                isDocumentoDuplicado = false;
                return;
            }

            let url = `index.php?view=clientes&action=check_documento&numero_documento=${encodeURIComponent(numeroDocumento)}`;
            if (idCliente) {
                url += `&id_cliente=${idCliente}`;
            }

            try {
                const response = await fetch(url);
                const data = await response.json();

                if (data.exists) {
                    errorDiv.textContent = 'Este número de documento ya está registrado.';
                    errorDiv.style.display = 'block';
                    submitBtn.disabled = true;
                    isDocumentoDuplicado = true;
                } else {
                    errorDiv.style.display = 'none';
                    submitBtn.disabled = false;
                    isDocumentoDuplicado = false;
                }
            } catch (error) {
                console.error('Error en la validación:', error);
                submitBtn.disabled = false;
                isDocumentoDuplicado = false;
            }
        };

        numeroDocumentoInput.addEventListener('blur', checkDocumento);

        let isSubmitting = false;
        clienteForm.addEventListener('submit', async function(e) {
            if (isSubmitting) {
                return;
            }
            e.preventDefault();
            await checkDocumento();

            if (isDocumentoDuplicado) {
                alert('No se puede guardar. El número de documento ya está registrado y en uso por otro cliente.');
                return;
            }

            isSubmitting = true;
            clienteForm.submit();
        });
    }
});
