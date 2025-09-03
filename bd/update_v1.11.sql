-- =================================================================
-- Update Script v1.11 (Restored)
-- Nuevos procedimientos para el Mantenimiento de Lista de Precios
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_lista_precios_buscar`
-- Busca en la lista de precios por un término en el nombre del curso o del tipo de precio.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_lista_precios_buscar`$$
CREATE PROCEDURE `sp_lista_precios_buscar`(IN p_term VARCHAR(100))
BEGIN
    SELECT
        lp.id_lista_precio,
        c.nombre AS curso_nombre,
        tp.nombre AS tipo_precio_nombre,
        lp.precio,
        lp.vigencia_inicio,
        lp.vigencia_fin
    FROM lista_precios lp
    JOIN cursos c ON lp.id_curso = c.id_curso
    JOIN tipos_precio tp ON lp.id_tipo_precio = tp.id_tipo_precio
    WHERE
        c.nombre LIKE CONCAT('%', p_term, '%') OR
        tp.nombre LIKE CONCAT('%', p_term, '%')
    ORDER BY c.nombre, lp.vigencia_inicio;
END$$

-- -----------------------------------------------------
-- `sp_cursos_listar_simple`
-- Lista solo ID y nombre de cursos para dropdowns.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_cursos_listar_simple`$$
CREATE PROCEDURE `sp_cursos_listar_simple`()
BEGIN
    SELECT id_curso, nombre FROM cursos ORDER BY nombre;
END$$

DELIMITER ;

-- =================================================================
-- Fin del Script v1.11
-- =================================================================
