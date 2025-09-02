-- =================================================================
-- Update Script v1.7
-- Nuevos procedimientos para el Mantenimiento de Tipos de Documento
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_tipos_documento_buscar`
-- Busca tipos de documento por un término en la descripción o código SUNAT.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_documento_buscar`$$
CREATE PROCEDURE `sp_tipos_documento_buscar`(IN p_term VARCHAR(100))
BEGIN
    SELECT
        id_tipo_documento,
        descripcion,
        longitud,
        codigo_sunat
    FROM tipos_documento
    WHERE
        descripcion LIKE CONCAT('%', p_term, '%') OR
        codigo_sunat LIKE CONCAT('%', p_term, '%')
    ORDER BY descripcion;
END$$


-- -----------------------------------------------------
-- `sp_tipos_documento_verificar_dependencias`
-- Verifica si un tipo de documento tiene clientes asociados.
-- Devuelve el conteo de clientes.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_documento_verificar_dependencias`$$
CREATE PROCEDURE `sp_tipos_documento_verificar_dependencias`(IN p_id_tipo_documento INT)
BEGIN
    SELECT COUNT(id_cliente) as count
    FROM clientes
    WHERE id_tipo_documento = p_id_tipo_documento;
END$$

DELIMITER ;

-- =================================================================
-- Fin del Script v1.7
-- =================================================================
