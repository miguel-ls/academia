-- =================================================================
-- Script de Creación de Procedimientos Almacenados para Programación
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `cursos_programados`
-- -----------------------------------------------------

DROP PROCEDURE IF EXISTS `sp_cursos_programados_listar`$$
CREATE PROCEDURE `sp_cursos_programados_listar`()
BEGIN
    SELECT
        cp.id_curso_programado,
        c.nombre AS curso_nombre,
        CONCAT(p.nombres, ' ', p.apellidos) AS profesor_nombre,
        CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area) AS ubicacion,
        cp.fecha_inicio,
        cp.fecha_fin,
        cp.estado
    FROM cursos_programados cp
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN profesores p ON cp.id_profesor = p.id_profesor
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    ORDER BY cp.fecha_inicio DESC;
END$$


DROP PROCEDURE IF EXISTS `sp_cursos_programados_obtener_por_id`$$
CREATE PROCEDURE `sp_cursos_programados_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT
        id_curso_programado,
        id_curso,
        id_profesor,
        id_sub_area,
        id_tipo_horario,
        fecha_inicio,
        fecha_fin,
        hora_inicio,
        hora_fin,
        vacantes_disponibles AS vacantes
    FROM cursos_programados
    WHERE id_curso_programado = p_id;
END$$


DROP PROCEDURE IF EXISTS `sp_cursos_programados_actualizar`$$
CREATE PROCEDURE `sp_cursos_programados_actualizar`(
    IN p_id_curso_programado INT,
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
    -- Se podría añadir lógica para no permitir la edición si el curso ya tiene matrículas,
    -- pero por ahora se mantiene simple.
    UPDATE cursos_programados
    SET
        id_curso = p_id_curso,
        id_profesor = p_id_profesor,
        id_sub_area = p_id_sub_area,
        id_tipo_horario = p_id_tipo_horario,
        fecha_inicio = p_fecha_inicio,
        fecha_fin = p_fecha_fin,
        hora_inicio = p_hora_inicio,
        hora_fin = p_hora_fin,
        vacantes_disponibles = p_vacantes
    WHERE id_curso_programado = p_id_curso_programado;
END$$


DROP PROCEDURE IF EXISTS `sp_cursos_programados_eliminar`$$
CREATE PROCEDURE `sp_cursos_programados_eliminar`(IN p_id INT)
BEGIN
    -- Idealmente, verificar si hay matrículas asociadas antes de eliminar.
    -- Si se elimina, también se deberían limpiar las asistencias generadas.
    DELETE FROM asistencia_profesor WHERE id_curso_programado = p_id;
    DELETE FROM cursos_programados WHERE id_curso_programado = p_id;
END$$


DELIMITER ;
