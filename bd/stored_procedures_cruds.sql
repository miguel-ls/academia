-- =================================================================
-- Script de Creación de Procedimientos Almacenados para CRUDs
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- Procedimientos para la tabla `tipos_area`
-- -----------------------------------------------------

CREATE PROCEDURE `sp_tipos_area_listar`()
BEGIN
    SELECT id_tipo_area, nombre FROM tipos_area ORDER BY nombre;
END$$

CREATE PROCEDURE `sp_tipos_area_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT id_tipo_area, nombre FROM tipos_area WHERE id_tipo_area = p_id;
END$$

CREATE PROCEDURE `sp_tipos_area_crear`(IN p_nombre VARCHAR(50))
BEGIN
    INSERT INTO tipos_area (nombre) VALUES (p_nombre);
END$$

CREATE PROCEDURE `sp_tipos_area_actualizar`(IN p_id INT, IN p_nombre VARCHAR(50))
BEGIN
    UPDATE tipos_area SET nombre = p_nombre WHERE id_tipo_area = p_id;
END$$

CREATE PROCEDURE `sp_tipos_area_eliminar`(IN p_id INT)
BEGIN
    -- Considerar la restricción de clave foránea.
    -- Esto fallará si un área está usando este tipo.
    DELETE FROM tipos_area WHERE id_tipo_area = p_id;
END$$


DELIMITER ;
