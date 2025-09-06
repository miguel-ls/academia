-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.35
-- Añade SP para el gráfico de barras apiladas del dashboard.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_dashboard_ventas_anuales_por_mes_y_curso`
-- Obtiene las ventas de un año específico, agrupado
-- por mes y por curso para el gráfico de barras apiladas.
-- -----------------------------------------------------

DROP PROCEDURE IF EXISTS `sp_dashboard_ventas_anuales_por_mes_y_curso`$$
CREATE PROCEDURE `sp_dashboard_ventas_anuales_por_mes_y_curso`(
    IN p_anio INT
)
BEGIN
    SELECT
        MONTH(m.fecha_matricula) AS `mes`,
        c.nombre AS `nombre_curso`,
        SUM(md.precio_final) AS `total_ventas`
    FROM matriculas m
    JOIN matriculas_detalle md ON m.id_matricula = md.id_matricula
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN cursos c ON cp.id_curso = c.id_curso
    WHERE
        YEAR(m.fecha_matricula) = p_anio
        AND m.estado = 'Activa'
    GROUP BY
        `mes`,
        `nombre_curso`
    ORDER BY
        `mes`,
        `nombre_curso`;
END$$

-- También se necesita eliminar el SP antiguo que ya no se usará
DROP PROCEDURE IF EXISTS `sp_dashboard_ventas_mensuales`$$

DELIMITER ;
