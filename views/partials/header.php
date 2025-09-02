<?php
// La configuración principal (config/config.php) ya se carga en index.php,
// por lo que SITE_URL y otras constantes ya deberían estar disponibles aquí.
$base_url = defined('SITE_URL') ? SITE_URL : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Academia</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    <style>
    /* Estilos para el menú desplegable */
    .dropdown { position: relative; display: inline-block; }
    .dropdown-content { display: none; position: absolute; background-color: #004a99; min-width: 200px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; border-radius: 4px; padding: 5px 0; }
    .dropdown-content a { color: white; padding: 12px 16px; text-decoration: none; display: block; text-align: left; }
    .dropdown-content a:hover { background-color: #0056b3; }
    .dropdown:hover .dropdown-content { display: block; }
    .nav-links { flex-grow: 1; }
    .nav-user { margin-left: auto; }
    header nav { justify-content: space-between; }
    </style>
</head>
<body>
    <header>
        <nav>
            <h1><a href="<?php echo $base_url; ?>index.php?view=dashboard">AcademiaSys</a></h1>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $base_url; ?>index.php?view=dashboard">Panel</a>

                    <div class="dropdown">
                        <a href="#">Configuración &#9662;</a>
                        <div class="dropdown-content">
                            <a href="<?php echo $base_url; ?>index.php?view=clientes">Clientes</a>
                            <a href="<?php echo $base_url; ?>index.php?view=cursos">Cursos</a>
                            <a href="<?php echo $base_url; ?>index.php?view=areas">Areas</a>
                            <a href="<?php echo $base_url; ?>index.php?view=sub_areas">Sub Areas</a>
                            <a href="<?php echo $base_url; ?>index.php?view=tipos_area">Tipos de Area</a>
                            <a href="<?php echo $base_url; ?>index.php?view=tipos_documento">Tipos de Documento</a>
                            <a href="<?php echo $base_url; ?>index.php?view=formas_pago">Formas de Pago</a>
                            <a href="<?php echo $base_url; ?>index.php?view=tipos_curso">Tipos de Curso</a>
                            <a href="<?php echo $base_url; ?>index.php?view=tipos_precio">Tipos de Precio</a>
                            <a href="<?php echo $base_url; ?>index.php?view=tipos_horario">Tipos de Horario</a>
                            <a href="<?php echo $base_url; ?>index.php?view=lista_precios">Lista de Precios</a>
                        </div>
                    </div>

                    <div class="dropdown">
                        <a href="#">Operaciones &#9662;</a>
                        <div class="dropdown-content">
                            <a href="<?php echo $base_url; ?>index.php?view=monitor">Monitor</a>
                            <a href="<?php echo $base_url; ?>index.php?view=clientes">Clientes</a>
                            <a href="<?php echo $base_url; ?>index.php?view=profesores">Profesores</a>
                            <a href="<?php echo $base_url; ?>index.php?view=matriculas">Matriculas</a>
                            <a href="<?php echo $base_url; ?>index.php?view=programar_horarios">Programar Horarios</a>
                            <a href="<?php echo $base_url; ?>index.php?view=asistencia_profesores">Asistencia Profesores</a>
                            <a href="<?php echo $base_url; ?>index.php?view=asistencia_clientes">Asistencia Clientes</a>
                            <a href="<?php echo $base_url; ?>index.php?view=calendario">Calendario</a>
                        </div>
                    </div>

                    <div class="dropdown">
                        <a href="#">Seguridad &#9662;</a>
                        <div class="dropdown-content">
                            <a href="<?php echo $base_url; ?>index.php?view=usuarios">Usuarios</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="nav-user">
                 <?php if (isset($_SESSION['user_id'])): ?>
                    <div style="text-align: right;">
                        <a href="<?php echo $base_url; ?>index.php?view=logout" style="font-weight: bold;">Cerrar Sesión</a>
                        <span style="color: white; font-size: 0.9em;"><?php echo htmlspecialchars($_SESSION['user_fullname']); ?></span>
                    </div>
                 <?php else: ?>
                    <a href="<?php echo $base_url; ?>index.php?view=login">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
    <div class="container"> <!-- Añadido container para envolver el contenido principal -->
