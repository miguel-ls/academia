-- =================================================================
-- Script de Actualizacion de Base de Datos - Version 1.37
-- Agrega el procedimiento para el grafico Ventas por Profesor.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_dashboard_ventas_por_profesor`$$
CREATE PROCEDURE `sp_dashboard_ventas_por_profesor`(
    IN p_anio INT,
    IN p_mes INT
)
BEGIN
    SELECT
        CONCAT(p.nombres, ' ', p.apellidos) AS `nombre_profesor`,
        SUM(md.precio_final) AS `total_ventas`
    FROM matriculas m
    JOIN matriculas_detalle md
        ON m.id_matricula = md.id_matricula
    JOIN cursos_programados cp
        ON md.id_curso_programado = cp.id_curso_programado
    JOIN profesores p
        ON cp.id_profesor = p.id_profesor
    WHERE
        YEAR(m.fecha_matricula) = p_anio
        AND MONTH(m.fecha_matricula) = p_mes
        AND m.estado = 'Activa'
    GROUP BY
        p.id_profesor,
        nombre_profesor
    ORDER BY
        total_ventas DESC,
        nombre_profesor ASC;
END$$

DELIMITER ;
