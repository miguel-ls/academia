<?php
// =================================================================
// Vista de Verificación de 2FA
// =================================================================
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Dos Factores - <?php echo SITE_NAME; ?></title>
    <!-- Reutilizamos el mismo estilo del login -->
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; text-align: center; }
        h1 { color: #333; }
        p { color: #555; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"] { width: 80%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; text-align: center; font-size: 1.2em; letter-spacing: 5px; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .feedback { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .error { background-color: #f8d7da; color: #721c24; }
        .simulation-notice { background-color: #fff3cd; color: #856404; padding: 10px; border: 1px solid #ffeeba; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="login-container">
        <h1>Verificación Requerida</h1>
        <p>Hemos enviado un código de seguridad a su correo. Por favor, ingréselo a continuación.</p>

        <?php if (!empty($feedback_message)): ?>
            <div class="feedback error">
                <?php echo htmlspecialchars($feedback_message); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?view=login&action=verify_2fa" method="POST">
            <input type="hidden" name="action" value="verify_2fa">
            <div class="form-group">
                <label for="code_2fa">Código de 6 dígitos:</label>
                <input type="text" id="code_2fa" name="code_2fa" maxlength="6" required autocomplete="off">
            </div>
            <button type="submit">Verificar y Entrar</button>
        </form>

        <!-- AVISO DE SIMULACIÓN: Esto se debe quitar en producción -->
        <?php if (isset($_SESSION['2fa_simulated_code'])): ?>
        <div class="simulation-notice">
            <strong>Aviso de Simulación:</strong><br>
            El envío de correos no está activo. Tu código es:<br>
            <h2><?php echo $_SESSION['2fa_simulated_code']; ?></h2>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>
