# Sistema de Gestión Académica - Matrícula de Cursos

Este es un sistema web completo desarrollado en PHP 7 y MySQL 5 para la gestión de una academia de cursos. El sistema permite la administración de cursos, alumnos, matrículas, horarios y más, con un enfoque en la seguridad y la organización del código.

Una característica arquitectónica clave de este sistema es que **no utiliza consultas SQL directas en el código PHP**. Toda la interacción con la base de datos se realiza a través de **procedimientos almacenados (Stored Procedures)**, lo que mejora significativamente la seguridad y el mantenimiento.

## Características Principales

-   **Gestión de Usuarios:** Mantenimiento de usuarios del sistema con perfiles (Administrador, Usuario).
-   **Seguridad:** Login con autenticación de dos factores (2FA) por correo simulado.
-   **Módulos de Configuración:** CRUDs completos para gestionar:
    -   Cursos, Áreas, Sub-áreas
    -   Clientes, Profesores
    -   Listas de Precios, Tipos de Horarios, Formas de Pago, etc.
-   **Módulos de Operaciones:**
    -   **Programación de Horarios:** Herramienta para programar cursos, lo que genera automáticamente los cronogramas de asistencia.
    -   **Página de Matrícula:** Un completo flujo de trabajo para matricular alumnos, con búsqueda de cursos, precios dinámicos y registro transaccional.
    -   **Monitor de Cursos:** Vista en tiempo real de los cursos con vacantes disponibles.
    -   **Anulación de Matrículas:** Funcionalidad para anular matrículas, devolviendo las vacantes a los cursos correspondientes.
-   **Panel Principal (Dashboard):** Un panel con gráficos dinámicos que muestran estadísticas de ventas, con filtros por año y mes.

## Requisitos

-   Servidor web (Apache, Nginx, etc.) con soporte para PHP 7.0 o superior.
-   Servidor de base de datos MySQL 5.x.
-   Un cliente de base de datos (como phpMyAdmin, MySQL Workbench) para importar los scripts.

## Instrucciones de Instalación

Siga estos pasos para instalar y configurar el sistema en su entorno local.

### 1. Configuración de la Base de Datos

1.  **Crear la Base de Datos:** Usando su cliente de MySQL, cree una nueva base de datos. Se recomienda el nombre `academia_cursos`.
    ```sql
    CREATE DATABASE academia_cursos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ```
2.  **Importar las Tablas:** Importe la estructura de las tablas ejecutando el contenido del archivo `bd/database_schema.sql`. Esto creará todas las tablas, relaciones y cargará algunos datos iniciales (roles, tipos de documento, etc.).
3.  **Importar los Procedimientos Almacenados:** Importe la lógica de negocio ejecutando el contenido del archivo `bd/stored_procedures.sql`. Esto creará todos los procedimientos almacenados que la aplicación necesita para funcionar.

### 2. Configuración del Proyecto

1.  **Copiar los Archivos:** Clone o copie todos los archivos del proyecto en el directorio raíz de su servidor web (ej. `htdocs` en XAMPP, `/var/www/html` en Linux).
2.  **Editar el Archivo de Configuración:** Abra el archivo `config/config.php`.
3.  **Ajustar las Credenciales:** Modifique las constantes de la base de datos con sus propios datos:
    ```php
    define('DB_HOST', '127.0.0.1');      // O 'localhost'
    define('DB_USER', 'tu_usuario_db'); // Su usuario de MySQL
    define('DB_PASS', 'tu_clave_db');   // Su contraseña de MySQL
    define('DB_NAME', 'academia_cursos'); // El nombre de la base de datos
    ```
4.  **Ajustar la URL del Sitio:** Modifique la constante `SITE_URL` para que coincida con la URL de su proyecto. Por ejemplo, si lo ha colocado en una carpeta llamada `academia`, la URL sería `http://localhost/academia`.

## Uso del Sistema

### Acceso

Una vez instalado, puede acceder al sistema a través de la URL que configuró.

-   **Usuario Administrador por Defecto:**
    -   **Usuario:** `admin`
    -   **Contraseña:** `123456`

Al iniciar sesión, el sistema le pedirá un código de verificación de dos factores. Dado que el envío de correos está simulado, el código necesario se mostrará en la misma página de verificación para facilitar el acceso durante las pruebas.

---
*Este proyecto ha sido generado por Jules, un asistente de ingeniería de software.*
