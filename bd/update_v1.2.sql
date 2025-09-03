-- =================================================================
-- Update Script v1.2 (Restored)
-- Nuevos procedimientos para el Mantenimiento de Cursos
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_cursos_buscar`
-- Busca cursos por un término en nombre, descripción o código ERP.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_cursos_buscar`$$
CREATE PROCEDURE `sp_cursos_buscar`(IN p_term VARCHAR(100))
BEGIN
    SELECT
        c.id_curso,
        c.nombre,
        c.descripcion,
        c.codigo_erp,
        tc.nombre AS tipo_curso
    FROM cursos c
    JOIN tipos_curso tc ON c.id_tipo_curso = tc.id_tipo_curso
    WHERE
        c.nombre LIKE CONCAT('%', p_term, '%') OR
        c.descripcion LIKE CONCAT('%', p_term, '%') OR
        c.codigo_erp LIKE CONCAT('%', p_term, '%')
    ORDER BY c.nombre;
END$$


-- -----------------------------------------------------
-- `sp_curso_verificar_dependencias`
-- Verifica si un curso tiene matrículas asociadas.
-- Devuelve el conteo de matrículas.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_curso_verificar_dependencias`$$
CREATE PROCEDURE `sp_curso_verificar_dependencias`(IN p_id_curso INT)
BEGIN
    SELECT COUNT(id_matricula) as count
    FROM matriculas
    WHERE id_curso = p_id_curso;
END$$

DELIMITER ;

-- =================================================================
-- Fin del Script v1.2
-- =================================================================
