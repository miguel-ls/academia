<?php

// =================================================================
// Controlador para la Programación de Horarios
// =================================================================

require_once 'models/ProgramacionModel.php';

// --- Verificación de Seguridad ---
// Solo los administradores pueden programar cursos.
if (!Session::isAdmin()) {
    echo "<h1>Acceso Denegado</h1>";
    exit();
}
// ---------------------------------

$programacionModel = new ProgramacionModel();
$feedback_message = '';

// --- Lógica para manejar acciones ---
$action = $_POST['action'] ?? 'show_form';

if ($action === 'programar') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $datos = [
                'id_curso' => $_POST['id_curso'],
                'id_profesor' => $_POST['id_profesor'],
                'id_sub_area' => $_POST['id_sub_area'],
                'id_tipo_horario' => $_POST['id_tipo_horario'],
                'fecha_inicio' => $_POST['fecha_inicio'],
                'fecha_fin' => $_POST['fecha_fin'],
                'hora_inicio' => $_POST['hora_inicio'],
                'hora_fin' => $_POST['hora_fin'],
                'vacantes' => $_POST['vacantes']
            ];

            // Validaciones básicas
            if (strtotime($datos['fecha_fin']) < strtotime($datos['fecha_inicio'])) {
                throw new Exception("La fecha de fin no puede ser anterior a la fecha de inicio.");
            }

            if ($programacionModel->programarCurso($datos)) {
                $feedback_message = "Curso programado y cronograma de asistencia generado exitosamente.";
            } else {
                throw new Exception("No se pudo programar el curso.");
            }
        } catch (Exception $e) {
            $feedback_message = "Error: " . $e->getMessage();
        }
    }
}

// --- Obtención de datos para la vista ---

// Siempre necesitamos las listas para el formulario
$listas_formulario = $programacionModel->obtenerListasParaFormulario();
$cursos = $listas_formulario['cursos'];
$profesores = $listas_formulario['profesores'];
$sub_areas = $listas_formulario['sub_areas'];
$tipos_horario = $listas_formulario['tipos_horario'];

// --- Cargar la Vista ---
require_once 'views/programar_horarios_view.php';
