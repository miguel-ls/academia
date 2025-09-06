-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.34
-- Añade SP para el nuevo gráfico del dashboard.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_dashboard_ventas_mes_por_curso_area`
-- Obtiene las ventas de un mes/año específico, agrupado
-- por la combinación de curso, área y subárea.
-- -----------------------------------------------------

DROP PROCEDURE IF EXISTS `sp_dashboard_ventas_mes_por_curso_area`$$
CREATE PROCEDURE `sp_dashboard_ventas_mes_por_curso_area`(
    IN p_anio INT,
    IN p_mes INT
)
BEGIN
    SELECT
        CONCAT(c.nombre, ' (', a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area, ')') AS `label`,
        SUM(md.precio_final) AS `total_ventas`
    FROM matriculas m
    JOIN matriculas_detalle md ON m.id_matricula = md.id_matricula
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    WHERE
        YEAR(m.fecha_matricula) = p_anio
        AND MONTH(m.fecha_matricula) = p_mes
        AND m.estado = 'Activa' -- o considerar también 'Completada'
    GROUP BY
        `label`
    ORDER BY
        `total_ventas` DESC;
END$$

DELIMITER ;
