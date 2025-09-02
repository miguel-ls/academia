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

-- -----------------------------------------------------
-- Procedimientos para la tabla `tipos_curso` (simple)
-- -----------------------------------------------------
CREATE PROCEDURE `sp_tipos_curso_listar`()
BEGIN
    SELECT id_tipo_curso, nombre FROM tipos_curso ORDER BY nombre;
END$$

-- -----------------------------------------------------
-- Procedimientos para la tabla `cursos`
-- -----------------------------------------------------

CREATE PROCEDURE `sp_cursos_listar`()
BEGIN
    SELECT c.id_curso, c.nombre, c.descripcion, c.codigo_erp, tc.nombre AS tipo_curso
    FROM cursos c
    JOIN tipos_curso tc ON c.id_tipo_curso = tc.id_tipo_curso
    ORDER BY c.nombre;
END$$

CREATE PROCEDURE `sp_cursos_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT id_curso, id_tipo_curso, nombre, descripcion, codigo_erp
    FROM cursos
    WHERE id_curso = p_id;
END$$

CREATE PROCEDURE `sp_cursos_crear`(
    IN p_id_tipo_curso INT,
    IN p_nombre VARCHAR(150),
    IN p_descripcion TEXT,
    IN p_codigo_erp VARCHAR(20)
)
BEGIN
    INSERT INTO cursos (id_tipo_curso, nombre, descripcion, codigo_erp)
    VALUES (p_id_tipo_curso, p_nombre, p_descripcion, p_codigo_erp);
END$$

CREATE PROCEDURE `sp_cursos_actualizar`(
    IN p_id_curso INT,
    IN p_id_tipo_curso INT,
    IN p_nombre VARCHAR(150),
    IN p_descripcion TEXT,
    IN p_codigo_erp VARCHAR(20)
)
BEGIN
    UPDATE cursos
    SET
        id_tipo_curso = p_id_tipo_curso,
        nombre = p_nombre,
        descripcion = p_descripcion,
        codigo_erp = p_codigo_erp
    WHERE id_curso = p_id_curso;
END$$

CREATE PROCEDURE `sp_cursos_eliminar`(IN p_id INT)
BEGIN
    DELETE FROM cursos WHERE id_curso = p_id;
END$$


DELIMITER ;
