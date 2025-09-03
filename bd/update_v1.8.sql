-- =================================================================
-- Update Script v1.8 (Restored)
-- Nuevos procedimientos para el Mantenimiento de Formas de Pago
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_formas_pago_buscar`
-- Busca formas de pago por un término en el nombre.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_formas_pago_buscar`$$
CREATE PROCEDURE `sp_formas_pago_buscar`(IN p_term VARCHAR(100))
BEGIN
    SELECT
        id_forma_pago,
        nombre
    FROM formas_pago
    WHERE
        nombre LIKE CONCAT('%', p_term, '%')
    ORDER BY nombre;
END$$


-- -----------------------------------------------------
-- `sp_formas_pago_verificar_dependencias`
-- Verifica si una forma de pago tiene matrículas asociadas.
-- Devuelve el conteo de matrículas.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_formas_pago_verificar_dependencias`$$
CREATE PROCEDURE `sp_formas_pago_verificar_dependencias`(IN p_id_forma_pago INT)
BEGIN
    SELECT COUNT(id_matricula) as count
    FROM matriculas
    WHERE id_forma_pago = p_id_forma_pago;
END$$

DELIMITER ;

-- =================================================================
-- Fin del Script v1.8
-- =================================================================
