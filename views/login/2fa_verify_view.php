<?php require_once 'views/partials/header.php'; ?>

<div class="form-container" style="max-width: 450px; margin-top: 5rem; text-align: center;">
    <h1>Verificación Requerida</h1>
    <p>Hemos enviado un código de seguridad a su correo. Por favor, ingréselo a continuación.</p>

    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<div class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        unset($_SESSION['error_message']);
    }
    ?>

    <form action="index.php?view=login&action=verify_2fa" method="POST">
        <input type="hidden" name="action" value="verify_2fa">
        <div class="form-group">
            <label for="code_2fa">Código de 6 dígitos:</label>
            <input type="text" id="code_2fa" name="code_2fa" maxlength="6" required autocomplete="off" style="text-align: center; font-size: 1.2em; letter-spacing: 5px;">
        </div>
        <div class="form-actions" style="border-top: none; padding-top: 0;">
            <button type="submit" class="btn btn-success" style="width: 100%;">Verificar y Entrar</button>
        </div>
    </form>

    <!-- AVISO DE SIMULACIÓN: Esto se debe quitar en producción -->
    <?php if (isset($_SESSION['2fa_simulated_code'])): ?>
    <div class="info-message" style="margin-top: 1rem;">
        <strong>Aviso de Simulación:</strong><br>
        El envío de correos no está activo. Tu código es:<br>
        <h2 style="margin: 0;"><?php echo $_SESSION['2fa_simulated_code']; ?></h2>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'views/partials/footer.php'; ?>
