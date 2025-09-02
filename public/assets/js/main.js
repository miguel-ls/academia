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

            // Si el campo está vacío o no ha cambiado (en modo edición), no hacer nada
            if (!numeroDocumento || (idCliente && numeroDocumento === originalDocumento)) {
                errorDiv.style.display = 'none';
                submitBtn.disabled = false;
                isDocumentoDuplicado = false;
                return;
            }

            // Construir la URL para la validación
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
                // En caso de error de red, permitir el envío para que valide el backend
                submitBtn.disabled = false;
                isDocumentoDuplicado = false;
            }
        };

        // Validar cuando el usuario deja el campo
        numeroDocumentoInput.addEventListener('blur', checkDocumento);

        // Prevenir el envío del formulario si hay un error
        clienteForm.addEventListener('submit', function(e) {
            if (isDocumentoDuplicado) {
                e.preventDefault();
                alert('No se puede guardar. El número de documento ya existe.');
            }
        });
    }
});
