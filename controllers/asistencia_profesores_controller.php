<?php

// =================================================================
// Controlador para la Asistencia de Profesores
// =================================================================

require_once 'models/AsistenciaProfesorModel.php';

// --- Verificación de Seguridad ---
Session::check();
// El placeholder original no tenía validación de rol, pero esta página
// debería ser solo para administradores o roles con permisos.
if (!Session::isAdmin()) {
    require_once 'views/partials/header.php';
    echo '<div class="page-header"><h1>Acceso Denegado</h1></div>';
    echo '<div class="card" style="padding: 20px;"><p>No tienes permiso para acceder a esta sección.</p>';
    echo '<a href="index.php?view=dashboard" class="btn">Volver al Panel</a></div>';
    require_once 'views/partials/footer.php';
    exit();
}
// ---------------------------------

$asistenciaModel = new AsistenciaProfesorModel();
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id_curso_programado = (int)($_GET['id'] ?? 0);

try {
    switch ($action) {
        case 'marcar':
            if ($id_curso_programado > 0) {
                // Lógica de Paginación
                $limit = 10;
                $pagina_actual = (int)($_GET['page'] ?? 1);
                $offset = ($pagina_actual - 1) * $limit;

                $total_clases = $asistenciaModel->contarClases($id_curso_programado);
                $total_paginas = ceil($total_clases / $limit);

                $detalle_curso = $asistenciaModel->obtenerDetalleCurso($id_curso_programado);
                $clases = $asistenciaModel->obtenerClases($id_curso_programado, $limit, $offset);

                if (!$detalle_curso) {
                    $_SESSION['feedback_message'] = "Error: El curso programado no fue encontrado.";
                    header('Location: index.php?view=asistencia_profesores');
                    exit();
                }

                require_once 'views/asistencia_profesor/form.php';
            } else {
                $_SESSION['feedback_message'] = "Error: ID de curso no válido.";
                header('Location: index.php?view=asistencia_profesores');
                exit();
            }
            break;

        case 'guardar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asistencia'])) {
                $asistencias = $_POST['asistencia'];
                $actualizaciones_exitosas = 0;
                $errores = 0;

                foreach ($asistencias as $id_asistencia => $datos) {
                    if ($asistenciaModel->actualizarAsistencia($id_asistencia, $datos['estado'], $datos['observaciones'])) {
                        $actualizaciones_exitosas++;
                    } else {
                        $errores++;
                    }
                }

                if ($errores > 0) {
                    $_SESSION['feedback_message'] = "Se actualizaron {$actualizaciones_exitosas} registros. Hubo {$errores} errores.";
                } else {
                    $_SESSION['feedback_message'] = "Se actualizaron {$actualizaciones_exitosas} registros de asistencia exitosamente.";
                }

            } else {
                 $_SESSION['feedback_message'] = "No se recibieron datos para guardar.";
            }
            header('Location: index.php?view=asistencia_profesores');
            exit();

        case 'list':
        default:
            $cursos_programados = $asistenciaModel->listarCursosProgramados();
            require_once 'views/asistencia_profesor/list.php';
            break;
    }

} catch (Exception $e) {
    $_SESSION['feedback_message'] = "Error inesperado en el sistema: " . $e->getMessage();
    header('Location: index.php?view=asistencia_profesores');
    exit();
}
