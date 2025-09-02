-- =================================================================
-- Update Script v1.12
-- Nuevo procedimiento para validación de duplicados en JS
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_cliente_verificar_documento_existente`
-- Verifica si un número de documento ya existe para otro cliente.
-- Devuelve 1 si existe, 0 si no.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_cliente_verificar_documento_existente`$$
CREATE PROCEDURE `sp_cliente_verificar_documento_existente`(
    IN p_numero_documento VARCHAR(20),
    IN p_id_cliente_excluir INT
)
BEGIN
    SELECT COUNT(id_cliente) as `exists`
    FROM clientes
    WHERE
        numero_documento = p_numero_documento AND
        (p_id_cliente_excluir IS NULL OR id_cliente != p_id_cliente_excluir);
END$$

DELIMITER ;

-- =================================================================
-- Fin del Script v1.12
-- =================================================================
