-- =================================================================
-- Update Script v1.4 (Restored)
-- Nuevos procedimientos para el Mantenimiento de Tipos de Area
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_tipos_area_buscar`
-- Busca tipos de area por un término en el nombre.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_area_buscar`$$
CREATE PROCEDURE `sp_tipos_area_buscar`(IN p_term VARCHAR(100))
BEGIN
    SELECT
        id_tipo_area,
        nombre
    FROM tipos_area
    WHERE
        nombre LIKE CONCAT('%', p_term, '%')
    ORDER BY nombre;
END$$


-- -----------------------------------------------------
-- `sp_tipos_area_verificar_dependencias`
-- Verifica si un tipo de area tiene areas asociadas.
-- Devuelve el conteo de areas.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_area_verificar_dependencias`$$
CREATE PROCEDURE `sp_tipos_area_verificar_dependencias`(IN p_id_tipo_area INT)
BEGIN
    SELECT COUNT(id_area) as count
    FROM areas
    WHERE id_tipo_area = p_id_tipo_area;
END$$

DELIMITER ;

-- =================================================================
-- Fin del Script v1.4
-- =================================================================
