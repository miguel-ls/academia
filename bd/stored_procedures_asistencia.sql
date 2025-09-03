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
        -- Se usa CAST para evitar errores de collation
        IF FIND_IN_SET(CAST(DAYOFWEEK(v_fecha_actual) AS CHAR), v_dias_semana) THEN
            INSERT INTO asistencia_profesor (id_curso_programado, id_profesor, fecha_clase, estado)
            VALUES (p_id_curso_programado, v_id_profesor, v_fecha_actual, 'Programado');
        END IF;
        SET v_fecha_actual = DATE_ADD(v_fecha_actual, INTERVAL 1 DAY);
    END WHILE;
END$$


DELIMITER ;
