<?php

// =================================================================
// Controlador para Cerrar Sesión (Logout)
// =================================================================

// La clase Session ya está cargada por index.php
// El método destroy() se encarga de limpiar la sesión y redirigir al login.
Session::destroy();
