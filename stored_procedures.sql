-- =================================================================
-- Script de Creación de Procedimientos Almacenados
-- Versión: 1.0
-- Motor: MySQL 5.x
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- Procedimientos para la tabla `usuarios`
-- -----------------------------------------------------

-- Crear un nuevo usuario
CREATE PROCEDURE `sp_usuarios_crear`(
    IN p_id_rol INT,
    IN p_nombre_usuario VARCHAR(50),
    IN p_password_hash VARCHAR(255),
    IN p_email VARCHAR(100),
    IN p_nombre_completo VARCHAR(150)
)
BEGIN
    INSERT INTO usuarios (id_rol, nombre_usuario, password_hash, email, nombre_completo, activo)
    VALUES (p_id_rol, p_nombre_usuario, p_password_hash, p_email, p_nombre_completo, 1);
END$$

-- Actualizar un usuario existente
CREATE PROCEDURE `sp_usuarios_actualizar`(
    IN p_id_usuario INT,
    IN p_id_rol INT,
    IN p_nombre_usuario VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_nombre_completo VARCHAR(150),
    IN p_activo TINYINT(1)
)
BEGIN
    UPDATE usuarios
    SET
        id_rol = p_id_rol,
        nombre_usuario = p_nombre_usuario,
        email = p_email,
        nombre_completo = p_nombre_completo,
        activo = p_activo
    WHERE id_usuario = p_id_usuario;
END$$

-- Cambiar contraseña de un usuario
CREATE PROCEDURE `sp_usuarios_cambiar_password`(
    IN p_id_usuario INT,
    IN p_password_hash VARCHAR(255)
)
BEGIN
    UPDATE usuarios
    SET password_hash = p_password_hash
    WHERE id_usuario = p_id_usuario;
END$$

-- Eliminar un usuario (borrado lógico)
CREATE PROCEDURE `sp_usuarios_eliminar`(
    IN p_id_usuario INT
)
BEGIN
    UPDATE usuarios
    SET activo = 0
    WHERE id_usuario = p_id_usuario;
END$$

-- Obtener un usuario por su ID
CREATE PROCEDURE `sp_usuarios_obtener_por_id`(
    IN p_id_usuario INT
)
BEGIN
    SELECT u.id_usuario, u.id_rol, r.nombre_rol, u.nombre_usuario, u.email, u.nombre_completo, u.activo, u.fecha_creacion
    FROM usuarios u
    JOIN roles r ON u.id_rol = r.id_rol
    WHERE u.id_usuario = p_id_usuario;
END$$

-- Obtener un usuario por su nombre de usuario (para login)
CREATE PROCEDURE `sp_usuarios_obtener_por_nombre`(
    IN p_nombre_usuario VARCHAR(50)
)
BEGIN
    SELECT id_usuario, id_rol, nombre_usuario, password_hash, email, nombre_completo, activo
    FROM usuarios
    WHERE nombre_usuario = p_nombre_usuario AND activo = 1;
END$$

-- Listar todos los usuarios
CREATE PROCEDURE `sp_usuarios_listar`()
BEGIN
    SELECT u.id_usuario, u.id_rol, r.nombre_rol, u.nombre_usuario, u.email, u.nombre_completo, u.activo
    FROM usuarios u
    JOIN roles r ON u.id_rol = r.id_rol
    ORDER BY u.nombre_completo;
END$$

-- Guardar código de 2FA
CREATE PROCEDURE `sp_usuarios_guardar_codigo_2fa`(
    IN p_id_usuario INT,
    IN p_codigo VARCHAR(10),
    IN p_expiracion DATETIME
)
BEGIN
    UPDATE usuarios
    SET auth_2fa_code = p_codigo,
        auth_2fa_expiry = p_expiracion
    WHERE id_usuario = p_id_usuario;
END$$

-- Verificar código de 2FA
CREATE PROCEDURE `sp_usuarios_verificar_codigo_2fa`(
    IN p_id_usuario INT,
    IN p_codigo VARCHAR(10)
)
BEGIN
    SELECT id_usuario
    FROM usuarios
    WHERE id_usuario = p_id_usuario
      AND auth_2fa_code = p_codigo
      AND auth_2fa_expiry > NOW();
END$$

-- -----------------------------------------------------
-- Procedimientos para la tabla `clientes`
-- -----------------------------------------------------

-- Crear un nuevo cliente
CREATE PROCEDURE `sp_clientes_crear`(
    IN p_id_tipo_documento INT,
    IN p_numero_documento VARCHAR(20),
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_codigo_erp VARCHAR(20)
)
BEGIN
    INSERT INTO clientes (id_tipo_documento, numero_documento, nombres, apellidos, email, telefono, codigo_erp)
    VALUES (p_id_tipo_documento, p_numero_documento, p_nombres, p_apellidos, p_email, p_telefono, p_codigo_erp);
    SELECT LAST_INSERT_ID() AS id_cliente;
END$$

-- Actualizar un cliente
CREATE PROCEDURE `sp_clientes_actualizar`(
    IN p_id_cliente INT,
    IN p_id_tipo_documento INT,
    IN p_numero_documento VARCHAR(20),
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_codigo_erp VARCHAR(20)
)
BEGIN
    UPDATE clientes
    SET
        id_tipo_documento = p_id_tipo_documento,
        numero_documento = p_numero_documento,
        nombres = p_nombres,
        apellidos = p_apellidos,
        email = p_email,
        telefono = p_telefono,
        codigo_erp = p_codigo_erp
    WHERE id_cliente = p_id_cliente;
END$$

-- Obtener un cliente por su ID
CREATE PROCEDURE `sp_clientes_obtener_por_id`(
    IN p_id_cliente INT
)
BEGIN
    SELECT c.*, td.descripcion as tipo_documento_desc
    FROM clientes c
    JOIN tipos_documento td ON c.id_tipo_documento = td.id_tipo_documento
    WHERE c.id_cliente = p_id_cliente;
END$$

-- Listar todos los clientes
CREATE PROCEDURE `sp_clientes_listar`()
BEGIN
    SELECT c.id_cliente, c.nombres, c.apellidos, td.descripcion as tipo_documento, c.numero_documento, c.email, c.telefono
    FROM clientes c
    JOIN tipos_documento td ON c.id_tipo_documento = td.id_tipo_documento
    ORDER BY c.apellidos, c.nombres;
END$$

-- Buscar clientes (para la matrícula)
CREATE PROCEDURE `sp_clientes_buscar`(
    IN p_termino_busqueda VARCHAR(100)
)
BEGIN
    SELECT id_cliente, numero_documento, CONCAT(nombres, ' ', apellidos) as nombre_completo
    FROM clientes
    WHERE numero_documento LIKE CONCAT('%', p_termino_busqueda, '%')
       OR nombres LIKE CONCAT('%', p_termino_busqueda, '%')
       OR apellidos LIKE CONCAT('%', p_termino_busqueda, '%');
END$$

-- -----------------------------------------------------
-- Procedimientos para la tabla `cursos`
-- -----------------------------------------------------

-- Listar cursos
CREATE PROCEDURE `sp_cursos_listar`()
BEGIN
    SELECT c.id_curso, c.nombre, tc.nombre as tipo_curso, c.descripcion, c.codigo_erp
    FROM cursos c
    JOIN tipos_curso tc ON c.id_tipo_curso = tc.id_tipo_curso
    ORDER BY c.nombre;
END$$

-- Crear curso
CREATE PROCEDURE `sp_cursos_crear`(
    IN p_id_tipo_curso INT,
    IN p_nombre VARCHAR(150),
    IN p_descripcion TEXT,
    IN p_codigo_erp VARCHAR(20)
)
BEGIN
    INSERT INTO cursos (id_tipo_curso, nombre, descripcion, codigo_erp)
    VALUES (p_id_tipo_curso, p_nombre, p_descripcion, p_codigo_erp);
END$$

-- etc... (Se añadirían aquí todos los demás procedimientos para las otras tablas:
-- areas, sub_areas, profesores, cursos_programados, matriculas, etc.
-- Por brevedad, se muestra una selección representativa)
-- El enfoque sería el mismo para cada tabla: Crear, Actualizar, Obtener, Listar, Eliminar (si aplica).

-- -----------------------------------------------------
-- Procedimientos para Programación de Cursos
-- -----------------------------------------------------

-- Crear una nueva programación de curso
CREATE PROCEDURE `sp_cursos_programados_crear`(
    IN p_id_curso INT,
    IN p_id_profesor INT,
    IN p_id_sub_area INT,
    IN p_id_tipo_horario INT,
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE,
    IN p_hora_inicio TIME,
    IN p_hora_fin TIME,
    IN p_vacantes INT
)
BEGIN
    -- Obtenemos la capacidad de la sub-área para validar las vacantes
    DECLARE v_capacidad INT;
    SELECT capacidad_maxima INTO v_capacidad FROM sub_areas WHERE id_sub_area = p_id_sub_area;

    IF p_vacantes > v_capacidad THEN
        SET p_vacantes = v_capacidad;
    END IF;

    INSERT INTO cursos_programados (id_curso, id_profesor, id_sub_area, id_tipo_horario, fecha_inicio, fecha_fin, hora_inicio, hora_fin, vacantes_disponibles)
    VALUES (p_id_curso, p_id_profesor, p_id_sub_area, p_id_tipo_horario, p_fecha_inicio, p_fecha_fin, p_hora_inicio, p_hora_fin, p_vacantes);

    SELECT LAST_INSERT_ID() AS id_curso_programado;
END$$

-- Generar el cronograma de asistencia para un profesor basado en un curso programado
CREATE PROCEDURE `sp_asistencia_profesor_generar_cronograma`(
    IN p_id_curso_programado INT
)
BEGIN
    DECLARE v_id_profesor INT;
    DECLARE v_fecha_inicio DATE;
    DECLARE v_fecha_fin DATE;
    DECLARE v_dias_semana VARCHAR(20);
    DECLARE v_fecha_actual DATE;

    -- Obtener los datos de la programación
    SELECT id_profesor, fecha_inicio, fecha_fin, th.dias_semana
    INTO v_id_profesor, v_fecha_inicio, v_fecha_fin, v_dias_semana
    FROM cursos_programados cp
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE cp.id_curso_programado = p_id_curso_programado;

    SET v_fecha_actual = v_fecha_inicio;

    -- Iterar día por día desde la fecha de inicio hasta la fecha de fin
    WHILE v_fecha_actual <= v_fecha_fin DO
        -- DAYOFWEEK devuelve 1 para Domingo, 2 para Lunes, etc.
        -- Hacemos un ajuste para que Lunes sea 1.
        SET @dia_de_la_semana = DAYOFWEEK(v_fecha_actual) - 1;
        IF @dia_de_la_semana = 0 THEN SET @dia_de_la_semana = 7; END IF;

        -- Si el día de la semana actual está en la cadena de días permitidos (ej. '1,3,5')
        IF FIND_IN_SET(CAST(@dia_de_la_semana AS CHAR), v_dias_semana) > 0 THEN
            INSERT INTO asistencia_profesor (id_curso_programado, id_profesor, fecha_clase, estado)
            VALUES (p_id_curso_programado, v_id_profesor, v_fecha_actual, 'Programado');
        END IF;

        SET v_fecha_actual = DATE_ADD(v_fecha_actual, INTERVAL 1 DAY);
    END WHILE;
END$$


-- Procedimientos simples para llenar dropdowns en el formulario
CREATE PROCEDURE `sp_profesores_listar_simple`()
BEGIN
    SELECT id_profesor, CONCAT(apellidos, ', ', nombres) as nombre_completo FROM profesores ORDER BY apellidos;
END$$

CREATE PROCEDURE `sp_cursos_listar_simple`()
BEGIN
    SELECT id_curso, nombre FROM cursos ORDER BY nombre;
END$$

CREATE PROCEDURE `sp_sub_areas_listar_simple`()
BEGIN
    SELECT sa.id_sub_area, CONCAT(a.nombre, ' - ', sa.descripcion) as nombre_completo
    FROM sub_areas sa
    JOIN areas a ON sa.id_area = a.id_area
    ORDER BY a.nombre, sa.descripcion;
END$$

CREATE PROCEDURE `sp_tipos_horario_listar_simple`()
BEGIN
    SELECT id_tipo_horario, descripcion FROM tipos_horario ORDER BY descripcion;
END$$


-- -----------------------------------------------------
-- Procedimientos para Matrículas
-- -----------------------------------------------------

CREATE PROCEDURE `sp_matriculas_listar`()
BEGIN
    SELECT
        m.id_matricula,
        CONCAT(c.nombres, ' ', c.apellidos) as nombre_cliente,
        m.fecha_matricula,
        m.monto_final,
        m.estado,
        u.nombre_usuario as registrado_por
    FROM matriculas m
    JOIN clientes c ON m.id_cliente = c.id_cliente
    JOIN usuarios u ON m.id_usuario_registro = u.id_usuario
    ORDER BY m.fecha_matricula DESC;
END$$

-- Registrar la cabecera de una matrícula y devolver el ID
CREATE PROCEDURE `sp_matricula_registrar_cabecera`(
    IN p_id_cliente INT,
    IN p_id_usuario_registro INT,
    IN p_id_forma_pago INT,
    IN p_fecha_inicio_clases DATE,
    IN p_fecha_fin_clases DATE,
    IN p_monto_total DECIMAL(10,2),
    IN p_descuento_total DECIMAL(10,2),
    IN p_monto_final DECIMAL(10,2),
    IN p_observaciones TEXT
)
BEGIN
    INSERT INTO matriculas (id_cliente, id_usuario_registro, id_forma_pago, fecha_inicio_clases, fecha_fin_clases, monto_total, descuento_total, monto_final, observaciones, estado)
    VALUES (p_id_cliente, p_id_usuario_registro, p_id_forma_pago, p_fecha_inicio_clases, p_fecha_fin_clases, p_monto_total, p_descuento_total, p_monto_final, p_observaciones, 'Activa');

    SELECT LAST_INSERT_ID() as id_matricula;
END$$

-- Registrar un detalle de matrícula y actualizar vacantes
CREATE PROCEDURE `sp_matricula_registrar_detalle`(
    IN p_id_matricula INT,
    IN p_id_curso_programado INT,
    IN p_precio_pactado DECIMAL(10,2),
    IN p_descuento DECIMAL(10,2),
    IN p_precio_final DECIMAL(10,2)
)
BEGIN
    -- Validar si aún hay vacantes
    DECLARE v_vacantes_disponibles INT;
    SELECT vacantes_disponibles INTO v_vacantes_disponibles
    FROM cursos_programados
    WHERE id_curso_programado = p_id_curso_programado;

    IF v_vacantes_disponibles > 0 THEN
        -- Insertar el detalle
        INSERT INTO matriculas_detalle (id_matricula, id_curso_programado, precio_pactado, descuento, precio_final)
        VALUES (p_id_matricula, p_id_curso_programado, p_precio_pactado, p_descuento, p_precio_final);

        -- Decrementar las vacantes
        UPDATE cursos_programados
        SET vacantes_disponibles = vacantes_disponibles - 1
        WHERE id_curso_programado = p_id_curso_programado;

        SELECT LAST_INSERT_ID() as id_matricula_detalle;
    ELSE
        -- Si no hay vacantes, devolvemos 0 o lanzamos un error
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay vacantes disponibles para este curso.';
    END IF;
END$$

-- Generar el cronograma de asistencia para un cliente matriculado
CREATE PROCEDURE `sp_asistencia_cliente_generar_cronograma`(
    IN p_id_matricula_detalle INT
)
BEGIN
    DECLARE v_id_cliente INT;
    DECLARE v_id_curso_programado INT;
    DECLARE v_fecha_inicio DATE;
    DECLARE v_fecha_fin DATE;
    DECLARE v_dias_semana VARCHAR(20);
    DECLARE v_fecha_actual DATE;

    -- Obtener datos necesarios
    SELECT m.id_cliente, md.id_curso_programado, cp.fecha_inicio, cp.fecha_fin, th.dias_semana
    INTO v_id_cliente, v_id_curso_programado, v_fecha_inicio, v_fecha_fin, v_dias_semana
    FROM matriculas_detalle md
    JOIN matriculas m ON md.id_matricula = m.id_matricula
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE md.id_matricula_detalle = p_id_matricula_detalle;

    SET v_fecha_actual = v_fecha_inicio;

    WHILE v_fecha_actual <= v_fecha_fin DO
        SET @dia_de_la_semana = DAYOFWEEK(v_fecha_actual) - 1;
        IF @dia_de_la_semana = 0 THEN SET @dia_de_la_semana = 7; END IF;

        IF FIND_IN_SET(CAST(@dia_de_la_semana AS CHAR), v_dias_semana) > 0 THEN
            INSERT INTO asistencia_cliente (id_matricula_detalle, id_cliente, fecha_clase, estado)
            VALUES (p_id_matricula_detalle, v_id_cliente, v_fecha_actual, 'Programado');
        END IF;

        SET v_fecha_actual = DATE_ADD(v_fecha_actual, INTERVAL 1 DAY);
    END WHILE;
END$$


-- Ejemplo de un procedimiento más complejo: buscar cursos disponibles para matrícula
CREATE PROCEDURE `sp_cursos_programados_buscar_disponibles`(
    IN p_profesor_id INT,
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    -- Este SP ya existía, pero para asegurar la consistencia del script,
    -- lo incluyo aquí de nuevo.
    SELECT
        cp.id_curso_programado,
        c.nombre as nombre_curso,
        CONCAT(p.nombres, ' ', p.apellidos) as nombre_profesor,
        cp.fecha_inicio,
        cp.fecha_fin,
        cp.hora_inicio,
        cp.hora_fin,
        th.descripcion as horario_dias,
        a.nombre as area,
        sa.descripcion as sub_area,
        cp.vacantes_disponibles,
        (SELECT pr.precio FROM lista_precios pr WHERE pr.id_curso = c.id_curso AND NOW() BETWEEN pr.vigencia_inicio AND pr.vigencia_fin ORDER BY pr.id_tipo_precio LIMIT 1) as precio_actual
    FROM cursos_programados cp
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN profesores p ON cp.id_profesor = p.id_profesor
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE cp.vacantes_disponibles > 0
      AND cp.estado = 'Programado'
      AND (p_profesor_id IS NULL OR cp.id_profesor = p_profesor_id)
      AND (p_fecha_inicio IS NULL OR cp.fecha_inicio >= p_fecha_inicio)
      AND (p_fecha_fin IS NULL OR cp.fecha_fin <= p_fecha_fin);
END$$

-- -----------------------------------------------------
-- Procedimientos para el Dashboard
-- -----------------------------------------------------

-- Obtener ventas totales por curso para un mes y año específicos (para gráfico de pastel)
CREATE PROCEDURE `sp_dashboard_ventas_por_curso`(
    IN p_anio INT,
    IN p_mes INT
)
BEGIN
    SELECT
        c.nombre as nombre_curso,
        SUM(md.precio_final) as total_ventas
    FROM matriculas_detalle md
    JOIN matriculas m ON md.id_matricula = m.id_matricula
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN cursos c ON cp.id_curso = c.id_curso
    WHERE YEAR(m.fecha_matricula) = p_anio AND MONTH(m.fecha_matricula) = p_mes
    GROUP BY c.nombre
    ORDER BY total_ventas DESC;
END$$

-- Obtener ventas mensuales (para gráfico de barras)
CREATE PROCEDURE `sp_dashboard_ventas_mensuales`(
    IN p_anio INT
)
BEGIN
    SELECT
        MONTH(fecha_matricula) as mes,
        SUM(monto_final) as total_ventas
    FROM matriculas m
    WHERE YEAR(fecha_matricula) = p_anio
    GROUP BY MONTH(fecha_matricula)
    ORDER BY mes;
END$$

-- -----------------------------------------------------
-- Procedimientos de Gestión Adicional
-- -----------------------------------------------------

-- Anular una matrícula y devolver las vacantes
CREATE PROCEDURE `sp_matricula_anular`(
    IN p_id_matricula INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_estado_actual VARCHAR(20);

    -- Iniciar transacción
    START TRANSACTION;

    -- Verificar que la matrícula no esté ya anulada
    SELECT estado INTO v_estado_actual FROM matriculas WHERE id_matricula = p_id_matricula;

    IF v_estado_actual = 'Activa' THEN
        -- 1. Actualizar el estado de la matrícula y añadir la observación
        UPDATE matriculas
        SET
            estado = 'Anulada',
            observaciones = CONCAT(IFNULL(observaciones, ''), '\nANULACIÓN: ', p_observaciones)
        WHERE id_matricula = p_id_matricula;

        -- 2. Devolver las vacantes de los cursos asociados a esta matrícula
        UPDATE cursos_programados cp
        JOIN matriculas_detalle md ON cp.id_curso_programado = md.id_curso_programado
        SET cp.vacantes_disponibles = cp.vacantes_disponibles + 1
        WHERE md.id_matricula = p_id_matricula;

        -- 3. Opcional: Cambiar el estado de la asistencia del cliente a 'Cancelado'
        UPDATE asistencia_cliente ac
        JOIN matriculas_detalle md ON ac.id_matricula_detalle = md.id_matricula_detalle
        SET ac.estado = 'Cancelado'
        WHERE md.id_matricula = p_id_matricula;

        COMMIT;
    ELSE
        -- Si ya está anulada o completada, no hacer nada y revertir
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La matrícula no se puede anular porque no está activa.';
    END IF;
END$$


DELIMITER ;
