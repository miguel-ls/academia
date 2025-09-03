-- =================================================================
-- Update Script v1.9 (Restored)
-- Nuevos procedimientos para el Mantenimiento de Tipos de Precio
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_tipos_precio_buscar`
-- Busca tipos de precio por un término en el nombre.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_precio_buscar`$$
CREATE PROCEDURE `sp_tipos_precio_buscar`(IN p_term VARCHAR(100))
BEGIN
    SELECT
        id_tipo_precio,
        nombre
    FROM tipos_precio
    WHERE
        nombre LIKE CONCAT('%', p_term, '%')
    ORDER BY nombre;
END$$


-- -----------------------------------------------------
-- `sp_tipos_precio_verificar_dependencias`
-- Verifica si un tipo de precio tiene listas de precios asociadas.
-- Devuelve el conteo de listas de precios.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_precio_verificar_dependencias`$$
CREATE PROCEDURE `sp_tipos_precio_verificar_dependencias`(IN p_id_tipo_precio INT)
BEGIN
    SELECT COUNT(id_lista_precio) as count
    FROM lista_precios
    WHERE id_tipo_precio = p_id_tipo_precio;
END$$

DELIMITER ;

-- =================================================================
-- Fin del Script v1.9
-- =================================================================
