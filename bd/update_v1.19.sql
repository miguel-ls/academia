-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.19
-- Corrige una posible versión desactualizada del SP sp_matricula_registrar_cabecera.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_matricula_registrar_cabecera`
-- Se asegura que la versión de este SP sea la correcta, utilizando
-- la tabla `matriculas` en lugar de una inexistente `matriculas_cabecera`.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matricula_registrar_cabecera`$$
CREATE PROCEDURE `sp_matricula_registrar_cabecera`(
    IN p_id_cliente INT,
    IN p_id_usuario_registro INT,
    IN p_id_forma_pago INT,
    IN p_fecha_inicio_clases DATE,
    IN p_fecha_fin_clases DATE,
    IN p_monto_total DECIMAL(10,2),
    IN p_descuento_total DECIMAL(10,2),
    IN p_monto_final DECIMAL(10,2),
    IN p_observaciones TEXT
)
BEGIN
    INSERT INTO matriculas (id_cliente, id_usuario_registro, id_forma_pago, fecha_inicio_clases, fecha_fin_clases, monto_total, descuento_total, monto_final, observaciones)
    VALUES (p_id_cliente, p_id_usuario_registro, p_id_forma_pago, p_fecha_inicio_clases, p_fecha_fin_clases, p_monto_total, p_descuento_total, p_monto_final, p_observaciones);

    SELECT LAST_INSERT_ID() as id_matricula;
END$$

DELIMITER ;
