-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.24
-- Corrige el SP `sp_matricula_obtener_cabecera_por_id` para que
-- devuelva también el ID de la forma de pago.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- SP: `sp_matricula_obtener_cabecera_por_id`
-- Definición corregida para incluir `id_forma_pago`.
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
        mc.id_forma_pago, -- Campo que faltaba
        fp.nombre AS forma_pago,
        mc.estado,
        mc.fecha_inicio_clases,
        mc.fecha_fin_clases
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
