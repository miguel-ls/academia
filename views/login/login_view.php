<?php require_once 'views/partials/header.php'; ?>

<div class="form-container" style="max-width: 400px; margin-top: 5rem;">
    <h1 style="text-align: center;">Iniciar Sesión</h1>

    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<div class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        unset($_SESSION['error_message']); // Limpiar el mensaje para que no se muestre de nuevo
    }
    ?>

    <form action="index.php?view=login" method="POST">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-actions" style="border-top: none; padding-top: 0;">
             <button type="submit" class="btn btn-primary" style="width: 100%;">Ingresar</button>
        </div>
    </form>
</div>

<?php
// El footer en la página de login usualmente es más simple o no existe.
// Para consistencia lo dejamos, pero podría ser removido.
require_once 'views/partials/footer.php';
?>
