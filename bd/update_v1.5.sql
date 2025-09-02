-- =================================================================
-- Update Script v1.5
-- Nuevos procedimientos para el Mantenimiento de Areas
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_areas_buscar`
-- Busca areas por un término en el nombre del área o el nombre del tipo de área.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_areas_buscar`$$
CREATE PROCEDURE `sp_areas_buscar`(IN p_term VARCHAR(100))
BEGIN
    SELECT
        a.id_area,
        a.nombre,
        ta.nombre AS tipo_area
    FROM areas a
    JOIN tipos_area ta ON a.id_tipo_area = ta.id_tipo_area
    WHERE
        a.nombre LIKE CONCAT('%', p_term, '%') OR
        ta.nombre LIKE CONCAT('%', p_term, '%')
    ORDER BY a.nombre;
END$$


-- -----------------------------------------------------
-- `sp_areas_verificar_dependencias`
-- Verifica si un area tiene sub-areas asociadas.
-- Devuelve el conteo de sub-areas.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_areas_verificar_dependencias`$$
CREATE PROCEDURE `sp_areas_verificar_dependencias`(IN p_id_area INT)
BEGIN
    SELECT COUNT(id_sub_area) as count
    FROM sub_areas
    WHERE id_area = p_id_area;
END$$

DELIMITER ;

-- =================================================================
-- Fin del Script v1.5
-- =================================================================
