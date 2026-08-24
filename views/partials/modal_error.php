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
            <button type="button" class="btn btn-primary" id="modal-accept-button">Aceptar</button>
        </div>
    </div>
</div>
<script>
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
