-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.21
-- Script de consolidación para corregir SPs de matrícula.
-- Este script redefine los procedimientos que el usuario encontró
-- desactualizados en su entorno.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- SP 1: `sp_matriculas_contar_por_curso`
-- Definición correcta para contar inscritos en cursos.
-- Usa la tabla `matriculas` y `estado = 'Activa'`.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matriculas_contar_por_curso`$$
CREATE PROCEDURE `sp_matriculas_contar_por_curso`(
    IN p_id_curso_programado INT
)
BEGIN
    SELECT
        COUNT(md.id_matricula_detalle) AS inscritos
    FROM
        matriculas_detalle md
    JOIN
        matriculas mc ON md.id_matricula = mc.id_matricula
    WHERE
        md.id_curso_programado = p_id_curso_programado
        AND mc.estado = 'Activa'; -- Contar solo matrículas activas
END$$

-- -----------------------------------------------------
-- SP 2: `sp_matricula_obtener_cabecera_por_id`
-- Definición correcta para obtener la cabecera de una matrícula.
-- Usa la tabla `matriculas` y compara el ENUM `estado` con strings.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matricula_obtener_cabecera_por_id`$$
CREATE PROCEDURE `sp_matricula_obtener_cabecera_por_id`(
    IN p_id_matricula INT
)
BEGIN
    SELECT
        mc.id_matricula,
        mc.id_cliente,
        CONCAT(c.nombres, ' ', c.apellidos) AS nombre_cliente,
        mc.fecha_matricula,
        mc.monto_total,
        mc.descuento_total,
        mc.monto_final,
        mc.observaciones,
        fp.nombre AS forma_pago,
        mc.estado
    FROM
        matriculas mc
    JOIN
        clientes c ON mc.id_cliente = c.id_cliente
    JOIN
        formas_pago fp ON mc.id_forma_pago = fp.id_forma_pago
    WHERE
        mc.id_matricula = p_id_matricula;
END$$

DELIMITER ;
