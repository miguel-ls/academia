<?php
// =================================================================
// Controlador para el Mantenimiento de Cursos (Refactorizado)
// =================================================================

require_once 'models/CursosModel.php';
require_once 'utils/NodeRedClient.php';

// --- Verificación de Seguridad ---
Session::check();
// ---------------------------------

$cursosModel = new CursosModel();

// --- Gestión de la Acción ---
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// --- Variables para la Vistas ---
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']);

$error_message = '';
$cursos = [];
$curso_a_editar = null;
$tipos_curso = [];
$categorias = [];
$grupos = [];
$clases = [];
$familias = [];
$search_term = '';

function sincronizarCursoConErp(array $datos) {
    $nodeRedClient = new NodeRedClient();
    return $nodeRedClient->request('POST', '/maestros/upsertcurso', [
        'Emp_cCodigo' => EMP_CCODIGO,
        'codigo_erp' => trim((string) $datos['codigo_erp']),
        'nombre' => trim((string) $datos['nombre']),
        'categoria_erp' => trim((string) $datos['categoria_erp']),
        'grupo_erp' => trim((string) $datos['grupo_erp']),
        'clase_erp' => trim((string) $datos['clase_erp']),
        'familia_erp' => trim((string) $datos['familia_erp']),
        'usuario_modificacion' => $_SESSION['user_name'] ?? 'sistema'
    ]);
}


try {
    switch ($action) {
        case 'obtener_grupos':
        case 'obtener_clases':
        case 'obtener_familias':
            header('Content-Type: application/json; charset=utf-8');
            try {
                $categoria = trim($_GET['categoria'] ?? '');
                $grupo = trim($_GET['grupo'] ?? '');
                $clase = trim($_GET['clase'] ?? '');

                if ($action === 'obtener_grupos') {
                    $datos = $categoria === '' ? [] : $cursosModel->obtenerGrupos($categoria);
                } elseif ($action === 'obtener_clases') {
                    $datos = ($categoria === '' || $grupo === '') ? [] : $cursosModel->obtenerClases($categoria, $grupo);
                } else {
                    $datos = ($categoria === '' || $grupo === '' || $clase === '') ? [] : $cursosModel->obtenerFamilias($categoria, $grupo, $clase);
                }
                echo json_encode(['success' => true, 'data' => $datos], JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'No se pudo obtener la clasificación.'], JSON_UNESCAPED_UNICODE);
            }
            exit();

        case 'migrate_classification':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?view=cursos');
                exit();
            }

            $nodeRedClient = new NodeRedClient();
            $resultado = $nodeRedClient->request('POST', '/maestros/migrarclasificacion', [
                'Emp_cCodigo' => EMP_CCODIGO
            ]);

            if ($resultado['success']) {
                $mensajeApi = is_array($resultado['data']) ? ($resultado['data']['message'] ?? '') : '';
                $_SESSION['feedback_message'] = 'Clasificación migrada exitosamente.' . ($mensajeApi ? ' ' . $mensajeApi : '');
            } else {
                $_SESSION['feedback_message'] = 'Error al migrar clasificación: ' . $resultado['error'];
            }

            header('Location: index.php?view=cursos');
            exit();

        case 'migrate':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?view=cursos');
                exit();
            }

            $nodeRedClient = new NodeRedClient();
            $resultado = $nodeRedClient->request('POST', '/maestros/migrarcursos', [
                'Emp_cCodigo' => EMP_CCODIGO
            ]);

            if ($resultado['success']) {
                $mensajeApi = is_array($resultado['data']) ? ($resultado['data']['message'] ?? '') : '';
                $_SESSION['feedback_message'] = 'Cursos migrados exitosamente.' . ($mensajeApi ? ' ' . $mensajeApi : '');
            } else {
                $_SESSION['feedback_message'] = 'Error al migrar cursos: ' . $resultado['error'];
            }

            header('Location: index.php?view=cursos');
            exit();

        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_tipo_curso' => $_POST['id_tipo_curso'],
                    'categoria_erp' => trim($_POST['categoria_erp'] ?? ''),
                    'grupo_erp' => trim($_POST['grupo_erp'] ?? ''),
                    'clase_erp' => trim($_POST['clase_erp'] ?? ''),
                    'familia_erp' => trim($_POST['familia_erp'] ?? ''),
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'],
                    'codigo_erp' => $_POST['codigo_erp']
                ];

                if (!$cursosModel->clasificacionExiste($datos['categoria_erp'], $datos['grupo_erp'], $datos['clase_erp'], $datos['familia_erp'])) {
                    $error_message = 'Seleccione una categoría, grupo, clase y familia válidos.';
                    $curso_a_editar = $datos;
                    $tipos_curso = $cursosModel->obtenerTiposDeCurso();
                    $categorias = $cursosModel->obtenerCategorias();
                    $grupos = $cursosModel->obtenerGrupos($datos['categoria_erp']);
                    $clases = $cursosModel->obtenerClases($datos['categoria_erp'], $datos['grupo_erp']);
                    $familias = $cursosModel->obtenerFamilias($datos['categoria_erp'], $datos['grupo_erp'], $datos['clase_erp']);
                    require_once 'views/cursos/form.php';
                    break;
                }

                $resultado = $cursosModel->crear($datos);
                if ($resultado['success']) {
                    $sincronizacion = sincronizarCursoConErp($datos);
                    if ($sincronizacion['success']) {
                        $_SESSION['feedback_message'] = 'Curso creado y sincronizado exitosamente.';
                    } else {
                        $_SESSION['feedback_message'] = 'Curso creado correctamente, pero no se pudo sincronizar con ERP: ' . $sincronizacion['error'];
                    }
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el curso: " . $resultado['error'];
                }
                header('Location: index.php?view=cursos');
                exit();
            }
            header('Location: index.php?view=cursos&action=new');
            exit();

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = [
                    'id_curso' => $_POST['id_curso'],
                    'id_tipo_curso' => $_POST['id_tipo_curso'],
                    'categoria_erp' => trim($_POST['categoria_erp'] ?? ''),
                    'grupo_erp' => trim($_POST['grupo_erp'] ?? ''),
                    'clase_erp' => trim($_POST['clase_erp'] ?? ''),
                    'familia_erp' => trim($_POST['familia_erp'] ?? ''),
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'],
                    'codigo_erp' => $_POST['codigo_erp']
                ];

                if (!$cursosModel->clasificacionExiste($datos['categoria_erp'], $datos['grupo_erp'], $datos['clase_erp'], $datos['familia_erp'])) {
                    $error_message = 'Seleccione una categoría, grupo, clase y familia válidos.';
                    $curso_a_editar = $datos;
                    $tipos_curso = $cursosModel->obtenerTiposDeCurso();
                    $categorias = $cursosModel->obtenerCategorias();
                    $grupos = $cursosModel->obtenerGrupos($datos['categoria_erp']);
                    $clases = $cursosModel->obtenerClases($datos['categoria_erp'], $datos['grupo_erp']);
                    $familias = $cursosModel->obtenerFamilias($datos['categoria_erp'], $datos['grupo_erp'], $datos['clase_erp']);
                    require_once 'views/cursos/form.php';
                    break;
                }

                $resultado = $cursosModel->actualizar($datos);

                if ($resultado['success']) {
                    $sincronizacion = sincronizarCursoConErp($datos);
                    if ($sincronizacion['success']) {
                        $_SESSION['feedback_message'] = 'Curso actualizado y sincronizado exitosamente.';
                    } else {
                        $_SESSION['feedback_message'] = 'Curso actualizado correctamente, pero no se pudo sincronizar con ERP: ' . $sincronizacion['error'];
                    }
                    header('Location: index.php?view=cursos');
                    exit();
                } else {
                    $error_message = "Error al actualizar: " . ($resultado['error'] ?? 'No se realizaron cambios.');
                    $curso_a_editar = $datos;
                    $tipos_curso = $cursosModel->obtenerTiposDeCurso();
                    $categorias = $cursosModel->obtenerCategorias();
                    $grupos = $cursosModel->obtenerGrupos($datos['categoria_erp']);
                    $clases = $cursosModel->obtenerClases($datos['categoria_erp'], $datos['grupo_erp']);
                    $familias = $cursosModel->obtenerFamilias($datos['categoria_erp'], $datos['grupo_erp'], $datos['clase_erp']);
                    require_once 'views/cursos/form.php';
                }
            } else {
                header('Location: index.php?view=cursos');
                exit();
            }
            break;

        case 'delete':
            if ($id > 0) {
                $dependencias = $cursosModel->verificarDependencias($id);
                if ($dependencias > 0) {
                    $_SESSION['feedback_message'] = "Error: No se puede eliminar el curso porque tiene {$dependencias} matrícula(s) asociada(s).";
                } else {
                    $resultado = $cursosModel->eliminar($id);
                    if ($resultado['success']) {
                        $_SESSION['feedback_message'] = "Curso eliminado exitosamente.";
                    } else {
                        $_SESSION['feedback_message'] = "Error: No se pudo eliminar el curso. " . ($resultado['error'] ?? '');
                    }
                }
            } else {
                $_SESSION['feedback_message'] = "Error: ID de curso no válido.";
            }
            header('Location: index.php?view=cursos');
            exit();

        case 'new':
            $tipos_curso = $cursosModel->obtenerTiposDeCurso();
            $categorias = $cursosModel->obtenerCategorias();
            require_once 'views/cursos/form.php';
            break;

        case 'edit':
            if ($id > 0) {
                $curso_a_editar = $cursosModel->obtenerPorId($id);
                $tipos_curso = $cursosModel->obtenerTiposDeCurso();
                $categorias = $cursosModel->obtenerCategorias();
                if (!$curso_a_editar) {
                    $_SESSION['feedback_message'] = "Error: Curso no encontrado.";
                    header('Location: index.php?view=cursos');
                    exit();
                }
                $grupos = $cursosModel->obtenerGrupos($curso_a_editar['categoria_erp']);
                $clases = $cursosModel->obtenerClases($curso_a_editar['categoria_erp'], $curso_a_editar['grupo_erp']);
                $familias = $cursosModel->obtenerFamilias($curso_a_editar['categoria_erp'], $curso_a_editar['grupo_erp'], $curso_a_editar['clase_erp']);
                require_once 'views/cursos/form.php';
            } else {
                 $_SESSION['feedback_message'] = "Error: ID de curso no válido.";
                 header('Location: index.php?view=cursos');
                 exit();
            }
            break;

        case 'list':
        default:
            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $cursos = $cursosModel->buscar($search_term);
            } else {
                $cursos = $cursosModel->obtenerTodos();
            }
            require_once 'views/cursos/list.php';
            break;
    }

} catch (Exception $e) {
    $error_message = "Error inesperado en el sistema: " . $e->getMessage();
    require_once 'views/cursos/list.php';
}
