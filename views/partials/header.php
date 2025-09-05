<?php
$base_url = defined('SITE_URL') ? SITE_URL : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('SITE_NAME') ? SITE_NAME : 'Sistema de Academia'; ?></title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/style.css">
</head>
<body>

<div class="app-wrapper">
    <?php if (isset($_SESSION['user_id'])): // Solo mostrar sidebar si hay sesión activa ?>
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo $base_url; ?>index.php?view=dashboard">AcademiaSys</a>
        </div>
        <ul class="sidebar-nav">
            <li><a href="<?php echo $base_url; ?>index.php?view=dashboard">Panel</a></li>

            <li class="nav-dropdown">
                <a href="#">Configuración</a>
                <ul class="dropdown-content">
                    <li><a href="<?php echo $base_url; ?>index.php?view=clientes">Clientes</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=profesores">Profesores</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=cursos">Cursos</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=areas">Areas</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=sub_areas">Sub Areas</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=tipos_area">Tipos de Area</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=tipos_documento">Tipos de Documento</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=formas_pago">Formas de Pago</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=tipos_curso">Tipos de Curso</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=tipos_precio">Tipos de Precio</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=tipos_horario">Tipos de Horario</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=lista_precios">Lista de Precios</a></li>
                </ul>
            </li>

            <li class="nav-dropdown">
                <a href="#">Operaciones</a>
                <ul class="dropdown-content">
                    <li><a href="<?php echo $base_url; ?>index.php?view=monitor">Monitor</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=matriculas">Matriculas</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=programar_horarios">Programar Horarios</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=asistencia_profesores">Asistencia Profesores</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=asistencia_clientes">Asistencia Clientes</a></li>
                    <li><a href="<?php echo $base_url; ?>index.php?view=calendario">Calendario</a></li>
                </ul>
            </li>

            <li class="nav-dropdown">
                <a href="#">Seguridad</a>
                <ul class="dropdown-content">
                    <li><a href="<?php echo $base_url; ?>index.php?view=usuarios">Usuarios</a></li>
                </ul>
            </li>
        </ul>
        <div class="sidebar-user">
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
            <!-- Podríamos tener un top-header aquí si quisiéramos -->
            <div class="content-body">
        <?php endif; ?>
