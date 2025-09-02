-- =================================================================
-- Script de Creación de Procedimientos Almacenados para CRUDs
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `tipos_area`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_area_listar`$$
CREATE PROCEDURE `sp_tipos_area_listar`() BEGIN SELECT id_tipo_area, nombre FROM tipos_area ORDER BY nombre; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_area_obtener_por_id`$$
CREATE PROCEDURE `sp_tipos_area_obtener_por_id`(IN p_id INT) BEGIN SELECT id_tipo_area, nombre FROM tipos_area WHERE id_tipo_area = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_area_crear`$$
CREATE PROCEDURE `sp_tipos_area_crear`(IN p_nombre VARCHAR(50)) BEGIN INSERT INTO tipos_area (nombre) VALUES (p_nombre); END$$

DROP PROCEDURE IF EXISTS `sp_tipos_area_actualizar`$$
CREATE PROCEDURE `sp_tipos_area_actualizar`(IN p_id INT, IN p_nombre VARCHAR(50)) BEGIN UPDATE tipos_area SET nombre = p_nombre WHERE id_tipo_area = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_area_eliminar`$$
CREATE PROCEDURE `sp_tipos_area_eliminar`(IN p_id INT) BEGIN DELETE FROM tipos_area WHERE id_tipo_area = p_id; END$$

-- -----------------------------------------------------
-- `tipos_curso`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_curso_listar`$$
CREATE PROCEDURE `sp_tipos_curso_listar`() BEGIN SELECT id_tipo_curso, nombre FROM tipos_curso ORDER BY nombre; END$$

-- -----------------------------------------------------
-- `cursos`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_cursos_listar`$$
CREATE PROCEDURE `sp_cursos_listar`() BEGIN SELECT c.id_curso, c.nombre, c.descripcion, c.codigo_erp, tc.nombre AS tipo_curso FROM cursos c JOIN tipos_curso tc ON c.id_tipo_curso = tc.id_tipo_curso ORDER BY c.nombre; END$$

DROP PROCEDURE IF EXISTS `sp_cursos_obtener_por_id`$$
CREATE PROCEDURE `sp_cursos_obtener_por_id`(IN p_id INT) BEGIN SELECT id_curso, id_tipo_curso, nombre, descripcion, codigo_erp FROM cursos WHERE id_curso = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_cursos_crear`$$
CREATE PROCEDURE `sp_cursos_crear`(IN p_id_tipo_curso INT, IN p_nombre VARCHAR(150), IN p_descripcion TEXT, IN p_codigo_erp VARCHAR(20)) BEGIN INSERT INTO cursos (id_tipo_curso, nombre, descripcion, codigo_erp) VALUES (p_id_tipo_curso, p_nombre, p_descripcion, p_codigo_erp); END$$

DROP PROCEDURE IF EXISTS `sp_cursos_actualizar`$$
CREATE PROCEDURE `sp_cursos_actualizar`(IN p_id_curso INT, IN p_id_tipo_curso INT, IN p_nombre VARCHAR(150), IN p_descripcion TEXT, IN p_codigo_erp VARCHAR(20)) BEGIN UPDATE cursos SET id_tipo_curso = p_id_tipo_curso, nombre = p_nombre, descripcion = p_descripcion, codigo_erp = p_codigo_erp WHERE id_curso = p_id_curso; END$$

DROP PROCEDURE IF EXISTS `sp_cursos_eliminar`$$
CREATE PROCEDURE `sp_cursos_eliminar`(IN p_id INT) BEGIN DELETE FROM cursos WHERE id_curso = p_id; END$$

-- -----------------------------------------------------
-- `areas`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_areas_listar`$$
CREATE PROCEDURE `sp_areas_listar`() BEGIN SELECT a.id_area, a.nombre, ta.nombre AS tipo_area FROM areas a JOIN tipos_area ta ON a.id_tipo_area = ta.id_tipo_area ORDER BY a.nombre; END$$

DROP PROCEDURE IF EXISTS `sp_areas_obtener_por_id`$$
CREATE PROCEDURE `sp_areas_obtener_por_id`(IN p_id INT) BEGIN SELECT id_area, id_tipo_area, nombre FROM areas WHERE id_area = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_areas_crear`$$
CREATE PROCEDURE `sp_areas_crear`(IN p_id_tipo_area INT, IN p_nombre VARCHAR(100)) BEGIN INSERT INTO areas (id_tipo_area, nombre) VALUES (p_id_tipo_area, p_nombre); END$$

DROP PROCEDURE IF EXISTS `sp_areas_actualizar`$$
CREATE PROCEDURE `sp_areas_actualizar`(IN p_id_area INT, IN p_id_tipo_area INT, IN p_nombre VARCHAR(100)) BEGIN UPDATE areas SET id_tipo_area = p_id_tipo_area, nombre = p_nombre WHERE id_area = p_id_area; END$$

DROP PROCEDURE IF EXISTS `sp_areas_eliminar`$$
CREATE PROCEDURE `sp_areas_eliminar`(IN p_id INT) BEGIN DELETE FROM areas WHERE id_area = p_id; END$$

-- -----------------------------------------------------
-- `sub_areas`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_sub_areas_listar`$$
CREATE PROCEDURE `sp_sub_areas_listar`()
BEGIN
    SELECT sa.id_sub_area, sa.descripcion, sa.numero_sub_area, sa.capacidad_maxima, a.nombre AS area_nombre
    FROM sub_areas sa
    JOIN areas a ON sa.id_area = a.id_area
    ORDER BY a.nombre, sa.descripcion;
END$$

DROP PROCEDURE IF EXISTS `sp_sub_areas_obtener_por_id`$$
CREATE PROCEDURE `sp_sub_areas_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT id_sub_area, id_area, descripcion, numero_sub_area, capacidad_maxima
    FROM sub_areas
    WHERE id_sub_area = p_id;
END$$

DROP PROCEDURE IF EXISTS `sp_sub_areas_crear`$$
CREATE PROCEDURE `sp_sub_areas_crear`(
    IN p_id_area INT,
    IN p_descripcion VARCHAR(100),
    IN p_numero_sub_area VARCHAR(20),
    IN p_capacidad_maxima INT
)
BEGIN
    INSERT INTO sub_areas (id_area, descripcion, numero_sub_area, capacidad_maxima)
    VALUES (p_id_area, p_descripcion, p_numero_sub_area, p_capacidad_maxima);
END$$

DROP PROCEDURE IF EXISTS `sp_sub_areas_actualizar`$$
CREATE PROCEDURE `sp_sub_areas_actualizar`(
    IN p_id_sub_area INT,
    IN p_id_area INT,
    IN p_descripcion VARCHAR(100),
    IN p_numero_sub_area VARCHAR(20),
    IN p_capacidad_maxima INT
)
BEGIN
    UPDATE sub_areas
    SET
        id_area = p_id_area,
        descripcion = p_descripcion,
        numero_sub_area = p_numero_sub_area,
        capacidad_maxima = p_capacidad_maxima
    WHERE id_sub_area = p_id_sub_area;
END$$

DROP PROCEDURE IF EXISTS `sp_sub_areas_eliminar`$$
CREATE PROCEDURE `sp_sub_areas_eliminar`(IN p_id INT)
BEGIN
    DELETE FROM sub_areas WHERE id_sub_area = p_id;
END$$

DELIMITER ;
