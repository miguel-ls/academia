-- =================================================================
-- Update Script v1.6
-- Nuevos procedimientos para el Mantenimiento de Sub Areas
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_sub_areas_buscar`
-- Busca sub-areas por un término en la descripción, número o nombre del área.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_sub_areas_buscar`$$
CREATE PROCEDURE `sp_sub_areas_buscar`(IN p_term VARCHAR(100))
BEGIN
    SELECT
        sa.id_sub_area,
        sa.descripcion,
        sa.numero_sub_area,
        sa.capacidad_maxima,
        a.nombre AS area_nombre
    FROM sub_areas sa
    JOIN areas a ON sa.id_area = a.id_area
    WHERE
        sa.descripcion LIKE CONCAT('%', p_term, '%') OR
        sa.numero_sub_area LIKE CONCAT('%', p_term, '%') OR
        a.nombre LIKE CONCAT('%', p_term, '%')
    ORDER BY a.nombre, sa.descripcion;
END$$


-- -----------------------------------------------------
-- `sp_sub_areas_verificar_dependencias`
-- Verifica si una sub-area tiene programaciones de horarios asociadas.
-- Devuelve el conteo de programaciones.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_sub_areas_verificar_dependencias`$$
CREATE PROCEDURE `sp_sub_areas_verificar_dependencias`(IN p_id_sub_area INT)
BEGIN
    SELECT COUNT(id_programacion) as count
    FROM programacion_horarios
    WHERE id_sub_area = p_id_sub_area;
END$$

DELIMITER ;

-- =================================================================
-- Fin del Script v1.6
-- =================================================================
