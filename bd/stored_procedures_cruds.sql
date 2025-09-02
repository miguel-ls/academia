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
CREATE PROCEDURE `sp_tipos_curso_listar`() BEGIN SELECT id_tipo_curso, nombre, descripcion FROM tipos_curso ORDER BY nombre; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_curso_obtener_por_id`$$
CREATE PROCEDURE `sp_tipos_curso_obtener_por_id`(IN p_id INT) BEGIN SELECT id_tipo_curso, nombre, descripcion FROM tipos_curso WHERE id_tipo_curso = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_curso_crear`$$
CREATE PROCEDURE `sp_tipos_curso_crear`(IN p_nombre VARCHAR(50), IN p_descripcion VARCHAR(255)) BEGIN INSERT INTO tipos_curso (nombre, descripcion) VALUES (p_nombre, p_descripcion); END$$

DROP PROCEDURE IF EXISTS `sp_tipos_curso_actualizar`$$
CREATE PROCEDURE `sp_tipos_curso_actualizar`(IN p_id INT, IN p_nombre VARCHAR(50), IN p_descripcion VARCHAR(255)) BEGIN UPDATE tipos_curso SET nombre = p_nombre, descripcion = p_descripcion WHERE id_tipo_curso = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_curso_eliminar`$$
CREATE PROCEDURE `sp_tipos_curso_eliminar`(IN p_id INT) BEGIN DELETE FROM tipos_curso WHERE id_tipo_curso = p_id; END$$

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

-- -----------------------------------------------------
-- `tipos_documento`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_documento_listar`$$
CREATE PROCEDURE `sp_tipos_documento_listar`()
BEGIN
    SELECT id_tipo_documento, descripcion, longitud, codigo_sunat FROM tipos_documento ORDER BY descripcion;
END$$

DROP PROCEDURE IF EXISTS `sp_tipos_documento_obtener_por_id`$$
CREATE PROCEDURE `sp_tipos_documento_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT id_tipo_documento, descripcion, longitud, codigo_sunat FROM tipos_documento WHERE id_tipo_documento = p_id;
END$$

DROP PROCEDURE IF EXISTS `sp_tipos_documento_crear`$$
CREATE PROCEDURE `sp_tipos_documento_crear`(
    IN p_descripcion VARCHAR(50),
    IN p_longitud INT,
    IN p_codigo_sunat VARCHAR(2)
)
BEGIN
    INSERT INTO tipos_documento (descripcion, longitud, codigo_sunat)
    VALUES (p_descripcion, p_longitud, p_codigo_sunat);
END$$

DROP PROCEDURE IF EXISTS `sp_tipos_documento_actualizar`$$
CREATE PROCEDURE `sp_tipos_documento_actualizar`(
    IN p_id INT,
    IN p_descripcion VARCHAR(50),
    IN p_longitud INT,
    IN p_codigo_sunat VARCHAR(2)
)
BEGIN
    UPDATE tipos_documento
    SET
        descripcion = p_descripcion,
        longitud = p_longitud,
        codigo_sunat = p_codigo_sunat
    WHERE id_tipo_documento = p_id;
END$$

DROP PROCEDURE IF EXISTS `sp_tipos_documento_eliminar`$$
CREATE PROCEDURE `sp_tipos_documento_eliminar`(IN p_id INT)
BEGIN
    DELETE FROM tipos_documento WHERE id_tipo_documento = p_id;
END$$

-- -----------------------------------------------------
-- `formas_pago`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_formas_pago_listar`$$
CREATE PROCEDURE `sp_formas_pago_listar`() BEGIN SELECT id_forma_pago, nombre FROM formas_pago ORDER BY nombre; END$$

DROP PROCEDURE IF EXISTS `sp_formas_pago_obtener_por_id`$$
CREATE PROCEDURE `sp_formas_pago_obtener_por_id`(IN p_id INT) BEGIN SELECT id_forma_pago, nombre FROM formas_pago WHERE id_forma_pago = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_formas_pago_crear`$$
CREATE PROCEDURE `sp_formas_pago_crear`(IN p_nombre VARCHAR(50)) BEGIN INSERT INTO formas_pago (nombre) VALUES (p_nombre); END$$

DROP PROCEDURE IF EXISTS `sp_formas_pago_actualizar`$$
CREATE PROCEDURE `sp_formas_pago_actualizar`(IN p_id INT, IN p_nombre VARCHAR(50)) BEGIN UPDATE formas_pago SET nombre = p_nombre WHERE id_forma_pago = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_formas_pago_eliminar`$$
CREATE PROCEDURE `sp_formas_pago_eliminar`(IN p_id INT) BEGIN DELETE FROM formas_pago WHERE id_forma_pago = p_id; END$$

-- -----------------------------------------------------
-- `tipos_precio`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_precio_listar`$$
CREATE PROCEDURE `sp_tipos_precio_listar`() BEGIN SELECT id_tipo_precio, nombre FROM tipos_precio ORDER BY nombre; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_precio_obtener_por_id`$$
CREATE PROCEDURE `sp_tipos_precio_obtener_por_id`(IN p_id INT) BEGIN SELECT id_tipo_precio, nombre FROM tipos_precio WHERE id_tipo_precio = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_precio_crear`$$
CREATE PROCEDURE `sp_tipos_precio_crear`(IN p_nombre VARCHAR(50)) BEGIN INSERT INTO tipos_precio (nombre) VALUES (p_nombre); END$$

DROP PROCEDURE IF EXISTS `sp_tipos_precio_actualizar`$$
CREATE PROCEDURE `sp_tipos_precio_actualizar`(IN p_id INT, IN p_nombre VARCHAR(50)) BEGIN UPDATE tipos_precio SET nombre = p_nombre WHERE id_tipo_precio = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_precio_eliminar`$$
CREATE PROCEDURE `sp_tipos_precio_eliminar`(IN p_id INT) BEGIN DELETE FROM tipos_precio WHERE id_tipo_precio = p_id; END$$

-- -----------------------------------------------------
-- `tipos_horario`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tipos_horario_listar`$$
CREATE PROCEDURE `sp_tipos_horario_listar`() BEGIN SELECT id_tipo_horario, descripcion, dias_semana FROM tipos_horario ORDER BY descripcion; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_horario_obtener_por_id`$$
CREATE PROCEDURE `sp_tipos_horario_obtener_por_id`(IN p_id INT) BEGIN SELECT id_tipo_horario, descripcion, dias_semana FROM tipos_horario WHERE id_tipo_horario = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_horario_crear`$$
CREATE PROCEDURE `sp_tipos_horario_crear`(IN p_descripcion VARCHAR(100), IN p_dias_semana VARCHAR(20)) BEGIN INSERT INTO tipos_horario (descripcion, dias_semana) VALUES (p_descripcion, p_dias_semana); END$$

DROP PROCEDURE IF EXISTS `sp_tipos_horario_actualizar`$$
CREATE PROCEDURE `sp_tipos_horario_actualizar`(IN p_id INT, IN p_descripcion VARCHAR(100), IN p_dias_semana VARCHAR(20)) BEGIN UPDATE tipos_horario SET descripcion = p_descripcion, dias_semana = p_dias_semana WHERE id_tipo_horario = p_id; END$$

DROP PROCEDURE IF EXISTS `sp_tipos_horario_eliminar`$$
CREATE PROCEDURE `sp_tipos_horario_eliminar`(IN p_id INT) BEGIN DELETE FROM tipos_horario WHERE id_tipo_horario = p_id; END$$

-- -----------------------------------------------------
-- `lista_precios`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_lista_precios_listar`$$
CREATE PROCEDURE `sp_lista_precios_listar`()
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
    ORDER BY c.nombre, lp.vigencia_inicio;
END$$

DROP PROCEDURE IF EXISTS `sp_lista_precios_obtener_por_id`$$
CREATE PROCEDURE `sp_lista_precios_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT id_lista_precio, id_curso, id_tipo_precio, precio, vigencia_inicio, vigencia_fin
    FROM lista_precios
    WHERE id_lista_precio = p_id;
END$$

DROP PROCEDURE IF EXISTS `sp_lista_precios_crear`$$
CREATE PROCEDURE `sp_lista_precios_crear`(
    IN p_id_curso INT,
    IN p_id_tipo_precio INT,
    IN p_precio DECIMAL(10,2),
    IN p_vigencia_inicio DATE,
    IN p_vigencia_fin DATE
)
BEGIN
    INSERT INTO lista_precios (id_curso, id_tipo_precio, precio, vigencia_inicio, vigencia_fin)
    VALUES (p_id_curso, p_id_tipo_precio, p_precio, p_vigencia_inicio, p_vigencia_fin);
END$$

DROP PROCEDURE IF EXISTS `sp_lista_precios_actualizar`$$
CREATE PROCEDURE `sp_lista_precios_actualizar`(
    IN p_id_lista_precio INT,
    IN p_id_curso INT,
    IN p_id_tipo_precio INT,
    IN p_precio DECIMAL(10,2),
    IN p_vigencia_inicio DATE,
    IN p_vigencia_fin DATE
)
BEGIN
    UPDATE lista_precios
    SET
        id_curso = p_id_curso,
        id_tipo_precio = p_id_tipo_precio,
        precio = p_precio,
        vigencia_inicio = p_vigencia_inicio,
        vigencia_fin = p_vigencia_fin
    WHERE id_lista_precio = p_id_lista_precio;
END$$

DROP PROCEDURE IF EXISTS `sp_lista_precios_eliminar`$$
CREATE PROCEDURE `sp_lista_precios_eliminar`(IN p_id INT)
BEGIN
    DELETE FROM lista_precios WHERE id_lista_precio = p_id;
END$$


DELIMITER ;
