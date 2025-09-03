-- =================================================================
-- Script de Creación de Procedimientos Almacenados para Asistencia
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `asistencia_profesor`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_asistencia_profesor_generar_cronograma`$$
CREATE PROCEDURE `sp_asistencia_profesor_generar_cronograma`(IN p_id_curso_programado INT)
BEGIN
    DECLARE v_fecha_actual DATE;
    DECLARE v_fecha_fin DATE;
    DECLARE v_id_profesor INT;
    DECLARE v_dias_semana VARCHAR(20);

    -- Obtener datos de la programación
    SELECT fecha_inicio, fecha_fin, id_profesor, th.dias_semana
    INTO v_fecha_actual, v_fecha_fin, v_id_profesor, v_dias_semana
    FROM cursos_programados cp
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE cp.id_curso_programado = p_id_curso_programado;

    -- Limpiar cronograma existente por si se regenera
    DELETE FROM asistencia_profesor WHERE id_curso_programado = p_id_curso_programado;

    -- Bucle para generar registros de asistencia
    WHILE v_fecha_actual <= v_fecha_fin DO
        -- DAYOFWEEK devuelve 1 para Domingo, 2 para Lunes, etc.
        -- Se inserta si el día de la semana está en la cadena de días permitidos
        -- Se usa COLLATE para forzar la codificación correcta y evitar errores
        IF FIND_IN_SET(DAYOFWEEK(v_fecha_actual), v_dias_semana COLLATE utf8mb4_unicode_ci) THEN
            INSERT INTO asistencia_profesor (id_curso_programado, id_profesor, fecha_clase, estado)
            VALUES (p_id_curso_programado, v_id_profesor, v_fecha_actual, 'Programado');
        END IF;
        SET v_fecha_actual = DATE_ADD(v_fecha_actual, INTERVAL 1 DAY);
    END WHILE;
END$$

-- `sp_asistencia_profesor_listar_cursos`
-- Lists all scheduled courses for the main attendance grid.
DROP PROCEDURE IF EXISTS `sp_asistencia_profesor_listar_cursos`$$
CREATE PROCEDURE `sp_asistencia_profesor_listar_cursos`()
BEGIN
    SELECT
        cp.id_curso_programado,
        c.nombre AS curso_nombre,
        CONCAT(p.nombres, ' ', p.apellidos) AS profesor_nombre,
        cp.fecha_inicio,
        cp.fecha_fin,
        cp.estado
    FROM cursos_programados cp
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN profesores p ON cp.id_profesor = p.id_profesor
    ORDER BY cp.fecha_inicio DESC;
END$$


-- `sp_asistencia_profesor_obtener_detalle_curso`
-- Gets the header details for the attendance marking page.
DROP PROCEDURE IF EXISTS `sp_asistencia_profesor_obtener_detalle_curso`$$
CREATE PROCEDURE `sp_asistencia_profesor_obtener_detalle_curso`(IN p_id_curso_programado INT)
BEGIN
    SELECT
        c.nombre AS curso_nombre,
        CONCAT(p.nombres, ' ', p.apellidos) AS profesor_nombre,
        CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area) AS ubicacion,
        th.descripcion AS tipo_horario_nombre,
        cp.fecha_inicio,
        cp.fecha_fin,
        cp.hora_inicio,
        cp.hora_fin
    FROM cursos_programados cp
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN profesores p ON cp.id_profesor = p.id_profesor
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE cp.id_curso_programado = p_id_curso_programado;
END$$


-- `sp_asistencia_profesor_obtener_clases`
-- Gets the list of individual class dates for a scheduled course (paginated).
DROP PROCEDURE IF EXISTS `sp_asistencia_profesor_obtener_clases`$$
CREATE PROCEDURE `sp_asistencia_profesor_obtener_clases`(
    IN p_id_curso_programado INT,
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
    SELECT
        id_asistencia_profesor,
        fecha_clase,
        estado,
        observaciones
    FROM asistencia_profesor
    WHERE id_curso_programado = p_id_curso_programado
    ORDER BY fecha_clase ASC
    LIMIT p_limit OFFSET p_offset;
END$$


-- `sp_asistencia_profesor_contar_clases`
-- Counts the total number of class dates for a scheduled course.
DROP PROCEDURE IF EXISTS `sp_asistencia_profesor_contar_clases`$$
CREATE PROCEDURE `sp_asistencia_profesor_contar_clases`(IN p_id_curso_programado INT)
BEGIN
    SELECT COUNT(id_asistencia_profesor) as total
    FROM asistencia_profesor
    WHERE id_curso_programado = p_id_curso_programado;
END$$


-- `sp_asistencia_profesor_actualizar_asistencia`
-- Updates the status and observations for a single class date.
DROP PROCEDURE IF EXISTS `sp_asistencia_profesor_actualizar_asistencia`$$
CREATE PROCEDURE `sp_asistencia_profesor_actualizar_asistencia`(
    IN p_id_asistencia_profesor INT,
    IN p_estado ENUM('Programado', 'Asistió', 'Faltó', 'Reprogramado'),
    IN p_observaciones VARCHAR(255)
)
BEGIN
    UPDATE asistencia_profesor
    SET
        estado = p_estado,
        observaciones = p_observaciones
    WHERE id_asistencia_profesor = p_id_asistencia_profesor;
END$$


DELIMITER ;
