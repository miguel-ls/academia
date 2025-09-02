<?php

// =================================================================
// Archivo de Funciones de Ayuda (Helpers)
// =================================================================

/**
 * Redirige al usuario a una URL usando JavaScript.
 * Es más robusto que header() ya que funciona incluso si ya se ha enviado salida.
 *
 * @param string $url La URL a la que redirigir.
 */
function redirect($url) {
    // Forzar el envío de la salida y terminar los buffers
    while (ob_get_level() > 0) {
        ob_end_flush();
    }
    flush();

    echo '<!DOCTYPE html><html><head><title>Redirigiendo...</title>';
    echo '<script>window.location.href = "' . htmlspecialchars($url, ENT_QUOTES) . '";</script>';
    echo '</head><body><p>Si no eres redirigido automáticamente, <a href="' . htmlspecialchars($url, ENT_QUOTES) . '">haz clic aquí</a>.</p></body></html>';

    // Detener la ejecución del script para asegurar que no se procese nada más.
    exit;
}
