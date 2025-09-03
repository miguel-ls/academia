-- =================================================================
-- Corrección para el Stored Procedure del Monitor de Cursos
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_cursos_programados_buscar_disponibles`$$
CREATE PROCEDURE `sp_cursos_programados_buscar_disponibles`(
    IN p_profesor_id INT,
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    SELECT
        cp.id_curso_programado,
        c.nombre AS nombre_curso,
        c.descripcion AS curso_descripcion,
        CONCAT(p.nombres, ' ', p.apellidos) AS nombre_profesor,
        a.nombre AS area,
        sa.descripcion AS sub_area,
        sa.numero_sub_area,
        cp.fecha_inicio,
        cp.fecha_fin,
        cp.hora_inicio,
        cp.hora_fin,
        th.descripcion as horario_dias,
        cp.vacantes_disponibles,
        (SELECT lp.precio FROM lista_precios lp WHERE lp.id_curso = c.id_curso AND lp.vigencia_inicio <= CURDATE() AND lp.vigencia_fin >= CURDATE() ORDER BY lp.id_tipo_precio LIMIT 1) AS precio_actual
    FROM cursos_programados cp
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN profesores p ON cp.id_profesor = p.id_profesor
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE
        cp.vacantes_disponibles > 0
        AND cp.estado = 'Programado'
        AND (p_profesor_id IS NULL OR cp.id_profesor = p_profesor_id)
        AND (p_fecha_inicio IS NULL OR cp.fecha_inicio >= p_fecha_inicio)
        AND (p_fecha_fin IS NULL OR cp.fecha_fin <= p_fecha_fin)
    ORDER BY cp.fecha_inicio;
END$$

DELIMITER ;
