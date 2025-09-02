-- =================================================================
-- Update Script v1.3
-- Nuevos procedimientos para el Mantenimiento de Tipos de Curso
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_tipos_curso_buscar`
-- Busca tipos de curso por un término en nombre o descripción.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_curso_buscar`$$
CREATE PROCEDURE `sp_tipos_curso_buscar`(IN p_term VARCHAR(100))
BEGIN
    SELECT
        id_tipo_curso,
        nombre,
        descripcion
    FROM tipos_curso
    WHERE
        nombre LIKE CONCAT('%', p_term, '%') OR
        descripcion LIKE CONCAT('%', p_term, '%')
    ORDER BY nombre;
END$$


-- -----------------------------------------------------
-- `sp_tipos_curso_verificar_dependencias`
-- Verifica si un tipo de curso tiene cursos asociados.
-- Devuelve el conteo de cursos.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_curso_verificar_dependencias`$$
CREATE PROCEDURE `sp_tipos_curso_verificar_dependencias`(IN p_id_tipo_curso INT)
BEGIN
    SELECT COUNT(id_curso) as count
    FROM cursos
    WHERE id_tipo_curso = p_id_tipo_curso;
END$$

DELIMITER ;

-- =================================================================
-- Fin del Script v1.3
-- =================================================================
