<style>
#error-modal.modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 2000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(15, 23, 42, .55);
}
#error-modal .modal-content {
    width: min(100%, 520px);
    overflow: hidden;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 18px 45px rgba(15, 23, 42, .25);
}
#error-modal .modal-header,
#error-modal .modal-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
}
#error-modal .modal-header { border-bottom: 1px solid #e2e8f0; }
#error-modal .modal-body { padding: 20px; color: #334155; }
#error-modal .modal-body p { margin: 0; white-space: pre-line; }
#error-modal .modal-close { color: #64748b; font-size: 24px; cursor: pointer; }
#error-modal .modal-footer { justify-content: flex-end; border-top: 1px solid #e2e8f0; }
</style>

<div id="error-modal" class="modal-overlay" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="error-modal-title">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="error-modal-title">Error</h2>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <p id="error-modal-message"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="modal-cancel-button" style="display: none;">Cancelar</button>
            <button type="button" class="btn btn-primary" id="modal-accept-button">Aceptar</button>
        </div>
    </div>
</div>
<script>
window.showMessageModal = function(message, options) {
    options = options || {};
    const modal = document.getElementById('error-modal');
    if (!modal) return false;

    const title = modal.querySelector('#error-modal-title');
    const messageNode = modal.querySelector('#error-modal-message');
    const acceptButton = modal.querySelector('#modal-accept-button');
    const cancelButton = modal.querySelector('#modal-cancel-button');
    const closeButton = modal.querySelector('.modal-close');

    title.textContent = options.title || 'Mensaje';
    messageNode.textContent = message;
    acceptButton.textContent = options.acceptLabel || 'Aceptar';
    cancelButton.textContent = options.cancelLabel || 'Cancelar';
    cancelButton.style.display = typeof options.onCancel === 'function' ? '' : 'none';

    const closeModal = function(cancelled) {
        modal.style.display = 'none';
        if (cancelled && typeof options.onCancel === 'function') {
            options.onCancel();
        } else if (typeof options.onClose === 'function') {
            options.onClose();
        }
    };

    const handleAccept = function() {
        closeModal(false);
        if (typeof options.onAccept === 'function') {
            options.onAccept();
        }
    };

    acceptButton.onclick = handleAccept;
    cancelButton.onclick = function() { closeModal(true); };
    closeButton.onclick = function() { closeModal(true); };
    modal.onclick = function(event) {
        if (event.target === modal) {
            closeModal(true);
        }
    };

    modal.style.display = 'flex';
    return true;
};

document.addEventListener('DOMContentLoaded', function() {
    const errorModal = document.getElementById('error-modal');
    if (!errorModal) return;
    const closeModal = function() { errorModal.style.display = 'none'; };
    errorModal.querySelector('.modal-close').addEventListener('click', closeModal);
    errorModal.querySelector('#modal-accept-button').addEventListener('click', closeModal);
    errorModal.addEventListener('click', function(event) {
        if (event.target === errorModal) closeModal();
    });
    <?php if (!empty($error_message)): ?>
    errorModal.querySelector('#error-modal-message').textContent = <?php echo json_encode($error_message, JSON_UNESCAPED_UNICODE); ?>;
    errorModal.style.display = 'flex';
    <?php endif; ?>
});
</script>
