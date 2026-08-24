<?php
$base_url = defined('SITE_URL') ? SITE_URL : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('SITE_NAME') ? SITE_NAME : 'Sistema de Academia'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/style.css">
</head>
<body>

<div class="app-wrapper">
    <?php if (isset($_SESSION['user_id'])): // Solo mostrar sidebar si hay sesión activa ?>
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo $base_url; ?>index.php?view=dashboard">
                <i class="bi bi-mortarboard-fill sidebar-logo" aria-hidden="true"></i>
                
            </a>
            <button type="button" id="sidebar-toggle" class="sidebar-toggle" aria-label="Ocultar barra lateral" aria-controls="sidebar" aria-expanded="true" title="Mostrar u ocultar barra lateral">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>
        </div>
        <ul class="sidebar-nav">
            <li><a href="<?php echo $base_url; ?>index.php?view=dashboard"><i class="bi bi-grid-1x2-fill nav-icon" aria-hidden="true"></i>Panel</a></li>

            <li class="nav-dropdown" data-menu="configuracion">
                <a href="#"><i class="bi bi-sliders nav-icon" aria-hidden="true"></i>Configuración</a>
                <ul class="dropdown-content">
                    <li><a href="<?php echo $base_url; ?>index.php?view=clientes"><i class="bi bi-people nav-icon" aria-hidden="true"></i>Clientes</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=profesores"><i class="bi bi-person-workspace nav-icon" aria-hidden="true"></i>Profesores</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=cursos"><i class="bi bi-journal-bookmark nav-icon" aria-hidden="true"></i>Cursos</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=areas"><i class="bi bi-building nav-icon" aria-hidden="true"></i>Areas</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=sub_areas"><i class="bi bi-diagram-3 nav-icon" aria-hidden="true"></i>Sub Areas</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=tipos_area"><i class="bi bi-tags nav-icon" aria-hidden="true"></i>Tipos de Area</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=tipos_documento"><i class="bi bi-card-text nav-icon" aria-hidden="true"></i>Tipos de Documento</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=formas_pago"><i class="bi bi-credit-card nav-icon" aria-hidden="true"></i>Formas de Pago</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=tipos_curso"><i class="bi bi-bookmark-star nav-icon" aria-hidden="true"></i>Tipos de Curso</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=tipos_precio"><i class="bi bi-tag nav-icon" aria-hidden="true"></i>Tipos de Precio</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=tipos_horario"><i class="bi bi-clock nav-icon" aria-hidden="true"></i>Tipos de Horario</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=lista_precios"><i class="bi bi-cash-stack nav-icon" aria-hidden="true"></i>Lista de Precios</a></li>
                </ul>
            </li>

            <li class="nav-dropdown" data-menu="operaciones">
                <a href="#"><i class="bi bi-activity nav-icon" aria-hidden="true"></i>Operaciones</a>
                <ul class="dropdown-content">
                    <li><a href="<?php echo $base_url; ?>index.php?view=monitor"><i class="bi bi-display nav-icon" aria-hidden="true"></i>Monitor</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=matriculas"><i class="bi bi-clipboard-check nav-icon" aria-hidden="true"></i>Matriculas</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=programar_horarios"><i class="bi bi-calendar2-week nav-icon" aria-hidden="true"></i>Programar Horarios</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=asistencia_profesores"><i class="bi bi-person-check nav-icon" aria-hidden="true"></i>Asistencia Profesores</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=asistencia_clientes"><i class="bi bi-people-fill nav-icon" aria-hidden="true"></i>Asistencia Clientes</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=calendario"><i class="bi bi-calendar3 nav-icon" aria-hidden="true"></i>Calendario</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=calendario_cursos"><i class="bi bi-calendar-event nav-icon" aria-hidden="true"></i>Calendario de Cursos</a></li>
                </ul>
            </li>

            <li class="nav-dropdown" data-menu="seguridad">
                <a href="#"><i class="bi bi-shield-lock nav-icon" aria-hidden="true"></i>Seguridad</a>
                <ul class="dropdown-content">
                    <li><a href="<?php echo $base_url; ?>index.php?view=usuarios"><i class="bi bi-person-gear nav-icon" aria-hidden="true"></i>Usuarios</a></li>
                </ul>
            </li>
        </ul>
        <div class="sidebar-user">
            <div><span>ERPPLUS - Academy</span>
        </div>
            <div>
                
                <span><?php echo htmlspecialchars($_SESSION['user_fullname']); ?></span>
                <a href="<?php echo $base_url; ?>index.php?view=logout">Salir</a>
            </div>
        </div>
    </aside>
    <?php endif; ?>

    <div class="main-content">
        <?php if (!isset($_SESSION['user_id'])): // Contenido para páginas sin login ?>
            <div class="login-wrapper">
                <div class="login-container">
        <?php else: ?>
            <div class="content-body">
        <?php endif; ?>
