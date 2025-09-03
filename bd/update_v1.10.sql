-- =================================================================
-- Update Script v1.10 (Restored)
-- Nuevos procedimientos para el Mantenimiento de Tipos de Horario
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_tipos_horario_buscar`
-- Busca tipos de horario por un término en la descripción.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_horario_buscar`$$
CREATE PROCEDURE `sp_tipos_horario_buscar`(IN p_term VARCHAR(100))
BEGIN
    SELECT
        id_tipo_horario,
        descripcion,
        dias_semana
    FROM tipos_horario
    WHERE
        descripcion LIKE CONCAT('%', p_term, '%')
    ORDER BY descripcion;
END$$


-- -----------------------------------------------------
-- `sp_tipos_horario_verificar_dependencias`
-- Verifica si un tipo de horario tiene programaciones asociadas.
-- Devuelve el conteo de programaciones.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_horario_verificar_dependencias`$$
CREATE PROCEDURE `sp_tipos_horario_verificar_dependencias`(IN p_id_tipo_horario INT)
BEGIN
    SELECT COUNT(id_programacion) as count
    FROM programacion_horarios
    WHERE id_tipo_horario = p_id_tipo_horario;
END$$

DELIMITER ;

-- =================================================================
-- Fin del Script v1.10
-- =================================================================
