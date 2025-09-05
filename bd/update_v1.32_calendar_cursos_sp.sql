-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.32
-- Añade SP para el nuevo calendario de cursos programados.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_calendario_programacion_listar`
-- Lista todos los cursos programados con datos extendidos
-- para ser usados por el nuevo "Calendario de Cursos".
-- -----------------------------------------------------

DROP PROCEDURE IF EXISTS `sp_calendario_programacion_listar`$$
CREATE PROCEDURE `sp_calendario_programacion_listar`(
    IN p_id_profesor INT,
    IN p_id_curso INT,
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    SELECT
        cp.id_curso_programado,
        c.id_curso,
        c.nombre AS curso_nombre,
        p.id_profesor,
        CONCAT(p.nombres, ' ', p.apellidos) AS profesor_nombre,
        a.id_area,
        sa.id_sub_area,
        CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area) AS ubicacion,
        th.descripcion AS tipo_horario_nombre,
        th.dias_semana,
        cp.fecha_inicio,
        cp.fecha_fin,
        cp.hora_inicio,
        cp.hora_fin,
        cp.vacantes_disponibles,
        cp.estado
    FROM cursos_programados cp
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN profesores p ON cp.id_profesor = p.id_profesor
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    WHERE
        (p_id_profesor IS NULL OR cp.id_profesor = p_id_profesor)
        AND (p_id_curso IS NULL OR cp.id_curso = p_id_curso)
        AND (p_fecha_inicio IS NULL OR cp.fecha_inicio >= p_fecha_inicio)
        AND (p_fecha_fin IS NULL OR cp.fecha_fin <= p_fecha_fin)
    ORDER BY cp.fecha_inicio DESC;
END$$

DELIMITER ;
